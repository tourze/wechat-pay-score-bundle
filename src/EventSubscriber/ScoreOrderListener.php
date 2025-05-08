<?php

namespace WechatPayScoreBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use WechatPayBundle\Service\WechatPayBuilder;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use Yiisoft\Json\Json;

/**
 * 目标：同步这个实体，同时操作远程
 *
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/open/pay/chapter3_1_2.shtml
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_14.shtml
 */
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: ScoreOrder::class)]
#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entity: ScoreOrder::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: ScoreOrder::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: ScoreOrder::class)]
class ScoreOrderListener
{
    public function __construct(
        private readonly WechatPayBuilder $payBuilder,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建本地前，同步一次到远程
     */
    public function prePersist(ScoreOrder $object): void
    {
        $requestJson = [
            'out_order_no' => $object->getOutTradeNo(),
            'appid' => $object->getAppId(),
            'service_id' => $object->getServiceId(),
            'service_introduction' => $object->getServiceIntroduction(),
            'time_range' => [
                'start_time' => $object->getStartTime(),
                'start_time_remark' => strval($object->getStartTimeRemark()),
            ],
            'risk_fund' => [
                'name' => $object->getRiskFundName(),
                'amount' => $object->getRiskFundAmount(),
                'description' => strval($object->getRiskFundDescription()),
            ],
            'notify_url' => $object->getNotifyUrl(),
        ];

        if ($object->getEndTime()) {
            $requestJson['time_range']['end_time'] = $object->getEndTime();
        }
        if ($object->getEndTimeRemark()) {
            $requestJson['time_range']['end_time_remark'] = $object->getEndTimeRemark();
        }

        if ($object->getPostPayments()->count() > 0) {
            $requestJson['post_payments'] = [];
            foreach ($object->getPostPayments() as $postPayment) {
                $requestJson['post_payments'][] = $postPayment->retrievePlainArray();
            }
        }
        if ($object->getPostDiscounts()->count() > 0) {
            $requestJson['post_discounts'] = [];
            foreach ($object->getPostDiscounts() as $postDiscount) {
                $requestJson['post_discounts'][] = $postDiscount->retrievePlainArray();
            }
        }

        $location = [];
        if ($object->getStartLocation()) {
            $location['start_location'] = $object->getStartLocation();
        }
        if ($object->getStartLocation()) {
            $location['end_location'] = $object->getEndLocation();
        }
        if (!empty($location)) {
            $requestJson['location'] = $location;
        }

        if ($object->getAttach()) {
            $requestJson['attach'] = $object->getAttach();
        }
        if ($object->getOpenId()) {
            $requestJson['openid'] = $object->getOpenId();
        }
        if (null !== $object->isNeedUserConfirm()) {
            $requestJson['need_user_confirm'] = $object->isNeedUserConfirm();
        }

        $builder = $this->payBuilder->genBuilder($object->getMerchant());
        $response = $builder->chain('v3/payscore/serviceorder')->post([
            'json' => $requestJson,
        ]);
        $response = $response->getBody()->getContents();
        $response = Json::decode($response);

        $object->setState($response['state']);
        $object->setStateDescription($response['state_description']);
        $object->setOrderId($response['order_id']);
        $object->setPackage($response['package']);
    }

    /**
     * 从数据库加载后，我们也检查下远程的状态
     */
    public function postLoad(ScoreOrder $object, PostLoadEventArgs $eventArgs): void
    {
        $builder = $this->payBuilder->genBuilder($object->getMerchant());
        $response = $builder->chain('v3/payscore/serviceorder')->get([
            'query' => [
                'out_order_no' => $object->getOutTradeNo(),
                'service_id' => $object->getServiceId(),
                'appid' => $object->getAppId(),
            ],
        ]);
        $response = $response->getBody()->getContents();
        $response = Json::decode($response);

        $object->setState($response['state']);
        $object->setStateDescription($response['state_description']);
        $object->setOrderId($response['order_id']);
        $object->setPackage($response['package']);
        $object->setTotalAmount($response['total_amount']);
        $object->setNeedCollection($response['need_collection']);
        $object->setCollection($response['collection']);

        $eventArgs->getObjectManager()->persist($object);
        $eventArgs->getObjectManager()->flush();
    }

    /**
     * 本地删除订单，远程就给他结束吧
     */
    public function preRemove(ScoreOrder $object): void
    {
        // 订单为以下状态时可以取消订单：CREATED（已创单）、DOING（进行中）（包括商户完结支付分订单后，且支付分订单收款状态为待支付USER_PAYING）
        if (!in_array($object->getState(), [ScoreOrderState::CREATED, ScoreOrderState::DOING])) {
            throw new \RuntimeException('无法取消交易分订单');
        }

        $builder = $this->payBuilder->genBuilder($object->getMerchant());
        $response = $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/cancel")->post([
            'json' => [
                'appid' => $object->getAppId(),
                'service_id' => $object->getServiceId(),
                'reason' => $object->getCancelReason(),
            ],
        ]);
        $response = $response->getBody()->getContents();
        $response = Json::decode($response);
        $this->logger->info('取消支付分订单结果', [
            'object' => $object,
            'response' => $response,
        ]);
    }

    /**
     * 更新订单信息
     */
    public function preUpdate(ScoreOrder $object, PreUpdateEventArgs $eventArgs): void
    {
        // 如果有修改订单状态
        if (isset($eventArgs->getEntityChangeSet()['state'])) {
            // 修改为已完结状态喔，那我们调用远程接口完结
            if (ScoreOrderState::DONE === $object->getState()) {
                $requestJson = [
                    'out_order_no' => $object->getOutTradeNo(),
                    'appid' => $object->getAppId(),
                    'service_id' => $object->getServiceId(),
                    'time_range' => [
                        'start_time' => $object->getStartTime(),
                        'start_time_remark' => strval($object->getStartTimeRemark()),
                        'end_time' => $object->getEndTime(),
                        'end_time_remark' => strval($object->getEndTimeRemark()),
                    ],
                    'total_amount' => $object->getTotalAmount(),
                ];
                if ($object->getEndLocation()) {
                    $requestJson['location'] = [
                        'end_location' => $object->getEndLocation(),
                    ];
                }
                if ($object->getPostPayments()->count() > 0) {
                    $requestJson['post_payments'] = [];
                    foreach ($object->getPostPayments() as $postPayment) {
                        $requestJson['post_payments'][] = $postPayment->retrievePlainArray();
                    }
                }
                if ($object->getPostDiscounts()->count() > 0) {
                    $requestJson['post_discounts'] = [];
                    foreach ($object->getPostDiscounts() as $postDiscount) {
                        $requestJson['post_discounts'][] = $postDiscount->retrievePlainArray();
                    }
                }

                $builder = $this->payBuilder->genBuilder($object->getMerchant());
                $response = $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/complete")->post([
                    'json' => $requestJson,
                ]);
                $response = $response->getBody()->getContents();
                $response = Json::decode($response);
                $this->logger->info('完结支付分订单结果', [
                    'object' => $object,
                    'response' => $response,
                ]);

                return;
            }
        }

        // 如果有改价原因
        if (isset($eventArgs->getEntityChangeSet()['modifyPriceReason'])) {
            $requestJson = [
                'out_order_no' => $object->getOutTradeNo(),
                'appid' => $object->getAppId(),
                'service_id' => $object->getServiceId(),
                'total_amount' => $object->getTotalAmount(),
                'reason' => $object->getModifyPriceReason(),
            ];
            if ($object->getPostPayments()->count() > 0) {
                $requestJson['post_payments'] = [];
                foreach ($object->getPostPayments() as $postPayment) {
                    $requestJson['post_payments'][] = $postPayment->retrievePlainArray();
                }
            }
            if ($object->getPostDiscounts()->count() > 0) {
                $requestJson['post_discounts'] = [];
                foreach ($object->getPostDiscounts() as $postDiscount) {
                    $requestJson['post_discounts'][] = $postDiscount->retrievePlainArray();
                }
            }

            $builder = $this->payBuilder->genBuilder($object->getMerchant());
            $response = $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/modify")->post([
                'json' => $requestJson,
            ]);
            $response = $response->getBody()->getContents();
            $response = Json::decode($response);
            $this->logger->info('修改订单金额结果', [
                'object' => $object,
                'response' => $response,
            ]);

            return;
        }
    }
}
