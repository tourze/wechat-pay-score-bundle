<?php

namespace WechatPayScoreBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

class CallbackController extends AbstractController
{
    public function __construct(
        private readonly ScoreOrderRepository $scoreOrderRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 支付成功回调
     *
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_22.shtml
     * @see https://github.com/wechatpay-apiv3/wechatpay-php#%E5%9B%9E%E8%B0%83%E9%80%9A%E7%9F%A5
     */
    #[Route(path: '/wechat-payment/score-order/success-callback/{outTradeNo}', name: 'wechat_payment_score_order_success_callback', methods: ['POST'])]
    public function paySuccess(string $outTradeNo, Request $request)
    {
        $scoreOrder = $this->scoreOrderRepository->findOneBy(['outTradeNo' => $outTradeNo]);
        if (!$scoreOrder) {
            throw new NotFoundHttpException();
        }

        $inWechatpaySignature = $request->headers->get('Wechatpay-Signature');
        $inWechatpayTimestamp = $request->headers->get('Wechatpay-Timestamp');
        $inWechatpaySerial = $request->headers->get('Wechatpay-Serial');
        $inWechatpayNonce = $request->headers->get('Wechatpay-Nonce');
        $inBody = $request->getContent();

        $apiv3Key = ''; // 在商户平台上设置的APIv3密钥

        // TODO 根据通知的平台证书序列号，查询本地平台证书文件，
        // 假定为 `/path/to/wechatpay/inWechatpaySerial.pem`
        $platformPublicKeyInstance = Rsa::from('file:///path/to/wechatpay/inWechatpaySerial.pem', Rsa::KEY_TYPE_PUBLIC);

        // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int) $inWechatpayTimestamp);
        $verifiedStatus = Rsa::verify(
            // 构造验签名串
            Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
            $inWechatpaySignature,
            $platformPublicKeyInstance,
        );
        if ($timeOffsetStatus && $verifiedStatus) {
            // 转换通知的JSON文本消息为PHP Array数组
            $inBodyArray = (array) json_decode($inBody, true);
            // 使用PHP7的数据解构语法，从Array中解构并赋值变量
            ['resource' => [
                'ciphertext' => $ciphertext,
                'nonce' => $nonce,
                'associated_data' => $aad,
            ]] = $inBodyArray;
            // 加密文本消息解密
            $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);
            // 把解密后的文本转换为PHP Array数组
            $inBodyResourceArray = (array) json_decode($inBodyResource, true);
            // print_r($inBodyResourceArray);// 打印解密后的结果
            $this->logger->info('得到解密结果', [
                'resource' => $inBodyResourceArray,
            ]);

            $event = new ScoreOrderCallbackEvent();
            $event->setScoreOrder($scoreOrder);
            $event->setEventType($inBodyArray['event_type']);
            $event->setResource($inBodyResourceArray);
            $this->eventDispatcher->dispatch($event);
        }

        return 'success';
    }
}
