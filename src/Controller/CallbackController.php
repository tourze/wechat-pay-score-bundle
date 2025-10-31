<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Controller;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

#[WithMonologChannel(channel: 'wechat_pay_score')]
final class CallbackController extends AbstractController
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
    public function __invoke(string $outTradeNo, Request $request): Response
    {
        $scoreOrder = $this->scoreOrderRepository->findOneBy(['outTradeNo' => $outTradeNo]);
        if (null === $scoreOrder) {
            throw new NotFoundHttpException();
        }

        $headers = $this->validateWechatPayHeaders($request);
        $apiv3Key = $this->getApiv3Key();
        $platformPublicKeyInstance = $this->getPlatformPublicKey($headers['serial']);

        if ($this->verifySignature($request, $headers, $platformPublicKeyInstance)) {
            $inBodyResourceArray = $this->decryptCallbackData($request->getContent(), $apiv3Key);
            $this->dispatchCallbackEvent($scoreOrder, $request->getContent(), $inBodyResourceArray);
        }

        return new Response('success');
    }

    /**
     * @return array{signature: string, timestamp: string, serial: string, nonce: string}
     */
    private function validateWechatPayHeaders(Request $request): array
    {
        $signature = $request->headers->get('Wechatpay-Signature');
        $timestamp = $request->headers->get('Wechatpay-Timestamp');
        $serial = $request->headers->get('Wechatpay-Serial');
        $nonce = $request->headers->get('Wechatpay-Nonce');

        if (null === $signature || '' === $signature || null === $timestamp || '' === $timestamp || null === $serial || '' === $serial || null === $nonce || '' === $nonce) {
            throw new \RuntimeException('Missing required WeChat Pay headers');
        }

        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
            'serial' => $serial,
            'nonce' => $nonce,
        ];
    }

    private function getApiv3Key(): string
    {
        $apiv3Key = $this->getParameter('wechat_pay_score.api_key');
        if (null === $apiv3Key || '' === $apiv3Key || !is_string($apiv3Key)) {
            throw new \RuntimeException('Missing or invalid wechat_pay_score.api_key configuration');
        }

        return $apiv3Key;
    }

    private function getPlatformPublicKey(string $serial): mixed
    {
        $certificatePath = $this->getParameter('wechat_pay_score.certificate_path');
        if (null === $certificatePath || '' === $certificatePath || !is_string($certificatePath)) {
            throw new \RuntimeException('Missing or invalid wechat_pay_score.certificate_path configuration');
        }

        $certPath = sprintf('%s/%s.pem', $certificatePath, $serial);
        if (!file_exists($certPath)) {
            throw new \RuntimeException(sprintf('Certificate file not found: %s', $certPath));
        }

        return Rsa::from(sprintf('file:///%s', $certPath), Rsa::KEY_TYPE_PUBLIC);
    }

    /**
     * @param array{signature: string, timestamp: string, serial: string, nonce: string} $headers
     */
    private function verifySignature(Request $request, array $headers, mixed $platformPublicKeyInstance): bool
    {
        $timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int) $headers['timestamp']);
        $verifiedStatus = Rsa::verify(
            Formatter::joinedByLineFeed($headers['timestamp'], $headers['nonce'], $request->getContent()),
            $headers['signature'],
            $platformPublicKeyInstance,
        );

        return $timeOffsetStatus && $verifiedStatus;
    }

    /**
     * @return array<string, mixed>
     */
    private function decryptCallbackData(string $inBody, string $apiv3Key): array
    {
        $inBodyArray = (array) json_decode($inBody, true);
        if (!isset($inBodyArray['resource']) || !is_array($inBodyArray['resource'])) {
            throw new \RuntimeException('Invalid callback payload: missing resource field');
        }

        $resource = $inBodyArray['resource'];
        if (!isset($resource['ciphertext'], $resource['nonce'], $resource['associated_data'])) {
            throw new \RuntimeException('Invalid callback payload: missing encryption fields');
        }

        if (!is_string($resource['ciphertext']) || !is_string($resource['nonce']) || !is_string($resource['associated_data'])) {
            throw new \RuntimeException('Invalid callback payload: encryption fields must be strings');
        }

        $inBodyResource = AesGcm::decrypt($resource['ciphertext'], $apiv3Key, $resource['nonce'], $resource['associated_data']);
        /** @var array<string, mixed> $inBodyResourceArray */
        $inBodyResourceArray = (array) json_decode($inBodyResource, true);

        $this->logger->info('得到解密结果', [
            'resource' => $inBodyResourceArray,
        ]);

        return $inBodyResourceArray;
    }

    /**
     * @param array<string, mixed> $inBodyResourceArray
     */
    private function dispatchCallbackEvent(ScoreOrder $scoreOrder, string $inBody, array $inBodyResourceArray): void
    {
        $inBodyArray = (array) json_decode($inBody, true);
        if (!isset($inBodyArray['event_type']) || !is_string($inBodyArray['event_type'])) {
            throw new \RuntimeException('Invalid callback payload: missing event_type field');
        }

        $event = new ScoreOrderCallbackEvent();
        $event->setScoreOrder($scoreOrder);
        $event->setEventType($inBodyArray['event_type']);
        $event->setResource($inBodyResourceArray);
        $this->eventDispatcher->dispatch($event);
    }
}
