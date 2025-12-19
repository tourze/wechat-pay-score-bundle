<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use WechatPayBundle\Service\WechatPayBuilder;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use WechatPayScoreBundle\Exception\MerchantRequiredException;
use WechatPayScoreBundle\Exception\ScoreOrderCancelException;
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
final class ScoreOrderListener
{
    public function __construct(
        private readonly WechatPayBuilder $payBuilder,
        private readonly LoggerInterface $logger,
        private readonly string $environment = 'prod',
    ) {
    }

    /**
     * 创建本地前，同步一次到远程
     */
    public function prePersist(ScoreOrder $object): void
    {
        if ('test' === $this->environment) {
            $this->logger->info('测试环境：跳过支付分订单创建请求', [
                'out_trade_no' => $object->getOutTradeNo(),
            ]);

            return;
        }

        $requestJson = $this->buildCreateRequestJson($object);

        $response = $this->executeApiCall(
            $object,
            '创建支付分订单',
            fn ($builder) => $builder->chain('v3/payscore/serviceorder')->post(['json' => $requestJson]),
            $requestJson
        );

        $this->updateOrderFromCreateResponse($object, $response);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCreateRequestJson(ScoreOrder $object): array
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

        $requestJson = $this->addOptionalTimeFields($requestJson, $object);
        $requestJson = $this->addPostPaymentsAndDiscounts($requestJson, $object);
        $requestJson = $this->addLocationData($requestJson, $object);

        return $this->addOptionalFields($requestJson, $object);
    }

    /**
     * @param array<string, mixed> $requestJson
     */
    /**
     * @param array<string, mixed> $requestJson
     * @return array<string, mixed>
     */
    private function addOptionalTimeFields(array $requestJson, ScoreOrder $object): array
    {
        if (!isset($requestJson['time_range']) || !is_array($requestJson['time_range'])) {
            $requestJson['time_range'] = [];
        }
        if (null !== $object->getEndTime()) {
            $requestJson['time_range']['end_time'] = $object->getEndTime();
        }
        if (null !== $object->getEndTimeRemark()) {
            $requestJson['time_range']['end_time_remark'] = $object->getEndTimeRemark();
        }

        return $requestJson;
    }

    /**
     * @param array<string, mixed> $requestJson
     */
    /**
     * @param array<string, mixed> $requestJson
     * @return array<string, mixed>
     */
    private function addPostPaymentsAndDiscounts(array $requestJson, ScoreOrder $object): array
    {
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

        return $requestJson;
    }

    /**
     * @param array<string, mixed> $requestJson
     */
    /**
     * @param array<string, mixed> $requestJson
     * @return array<string, mixed>
     */
    private function addLocationData(array $requestJson, ScoreOrder $object): array
    {
        $location = [];
        if (null !== $object->getStartLocation()) {
            $location['start_location'] = $object->getStartLocation();
        }
        if (null !== $object->getEndLocation()) {
            $location['end_location'] = $object->getEndLocation();
        }
        if ([] !== $location) {
            $requestJson['location'] = $location;
        }

        return $requestJson;
    }

    /**
     * @param array<string, mixed> $requestJson
     */
    /**
     * @param array<string, mixed> $requestJson
     * @return array<string, mixed>
     */
    private function addOptionalFields(array $requestJson, ScoreOrder $object): array
    {
        if (null !== $object->getAttach()) {
            $requestJson['attach'] = $object->getAttach();
        }
        if (null !== $object->getOpenId()) {
            $requestJson['openid'] = $object->getOpenId();
        }
        if (null !== $object->isNeedUserConfirm()) {
            $requestJson['need_user_confirm'] = $object->isNeedUserConfirm();
        }

        return $requestJson;
    }

    /**
     * 执行微信支付API调用的通用方法
     *
     * @param callable $apiCall 接收 WechatPayBuilder 返回 ResponseInterface 的闭包
     * @param array<string, mixed> $requestData 请求数据用于日志
     * @return array<string, mixed>
     */
    private function executeApiCall(ScoreOrder $object, string $operation, callable $apiCall, array $requestData): array
    {
        $this->logger->info("{$operation}请求", [
            'out_trade_no' => $object->getOutTradeNo(),
            'request' => $requestData,
        ]);

        $startTime = microtime(true);
        $merchant = $object->getMerchant();
        if (null === $merchant) {
            throw new MerchantRequiredException('ScoreOrder must have a merchant');
        }
        $builder = $this->payBuilder->genBuilder($merchant);

        try {
            $response = $apiCall($builder);
            $responseBody = $response->getBody()->getContents();
            $decoded = Json::decode($responseBody);

            $this->logger->info("{$operation}响应", [
                'out_trade_no' => $object->getOutTradeNo(),
                'response' => $decoded,
                'elapsed_time' => (microtime(true) - $startTime) . 's',
            ]);

            if (!is_array($decoded)) {
                throw new \RuntimeException('Invalid response: expected array');
            }

            /** @var array<string, mixed> */
            return $decoded;
        } catch (\Exception $e) {
            $this->logger->error("{$operation}失败", [
                'out_trade_no' => $object->getOutTradeNo(),
                'error' => $e->getMessage(),
                'elapsed_time' => (microtime(true) - $startTime) . 's',
            ]);
            throw $e;
        }
    }

    /**
     * 从查询响应中更新订单状态
     *
     * @param array<string, mixed> $response
     */
    private function updateOrderFromQueryResponse(ScoreOrder $object, array $response): void
    {
        $this->updateOrderFromCreateResponse($object, $response);
        $this->updateOrderExtendedFields($object, $response);
    }

    /**
     * 更新订单的扩展字段（查询时返回的额外信息）
     *
     * @param array<string, mixed> $response
     */
    private function updateOrderExtendedFields(ScoreOrder $object, array $response): void
    {
        $totalAmount = isset($response['total_amount']) && is_int($response['total_amount']) ? $response['total_amount'] : null;
        $object->setTotalAmount($totalAmount);

        $needCollection = isset($response['need_collection']) && is_bool($response['need_collection']) ? $response['need_collection'] : null;
        $object->setNeedCollection($needCollection);

        /** @var array<string, mixed>|null $collection */
        $collection = isset($response['collection']) && is_array($response['collection']) ? $response['collection'] : null;
        $object->setCollection($collection);
    }

    /**
     * 从创建响应中更新订单基础信息
     *
     * @param array<string, mixed> $response
     */
    private function updateOrderFromCreateResponse(ScoreOrder $object, array $response): void
    {
        if (!isset($response['state']) || !is_string($response['state'])) {
            throw new \RuntimeException('Invalid response: missing state field');
        }

        $object->setState(ScoreOrderState::from($response['state']));

        $stateDescription = isset($response['state_description']) && is_string($response['state_description']) ? $response['state_description'] : null;
        $object->setStateDescription($stateDescription);

        $orderId = isset($response['order_id']) && is_string($response['order_id']) ? $response['order_id'] : null;
        $object->setOrderId($orderId);

        $package = isset($response['package']) && is_string($response['package']) ? $response['package'] : null;
        $object->setPackage($package);
    }

    /**
     * 从数据库加载后，我们也检查下远程的状态
     */
    public function postLoad(ScoreOrder $object, PostLoadEventArgs $eventArgs): void
    {
        if ('test' === $this->environment) {
            return;
        }
        $queryParams = [
            'out_order_no' => $object->getOutTradeNo(),
            'service_id' => $object->getServiceId(),
            'appid' => $object->getAppId(),
        ];

        $response = $this->executeApiCall(
            $object,
            '查询支付分订单状态',
            fn ($builder) => $builder->chain('v3/payscore/serviceorder')->get(['query' => $queryParams]),
            ['query' => $queryParams]
        );

        $this->updateOrderFromQueryResponse($object, $response);

        $eventArgs->getObjectManager()->persist($object);
        $eventArgs->getObjectManager()->flush();
    }

    /**
     * 本地删除订单，远程就给他结束吧
     */
    public function preRemove(ScoreOrder $object): void
    {
        if ('test' === $this->environment) {
            return;
        }
        // 订单为以下状态时可以取消订单：CREATED（已创单）、DOING（进行中）（包括商户完结支付分订单后，且支付分订单收款状态为待支付USER_PAYING）
        if (!in_array($object->getState(), [ScoreOrderState::CREATED, ScoreOrderState::DOING], true)) {
            throw new ScoreOrderCancelException('无法取消交易分订单');
        }

        $requestJson = [
            'appid' => $object->getAppId(),
            'service_id' => $object->getServiceId(),
            'reason' => $object->getCancelReason(),
        ];

        $this->executeApiCall(
            $object,
            '取消支付分订单',
            fn ($builder) => $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/cancel")->post(['json' => $requestJson]),
            $requestJson
        );
    }

    /**
     * 更新订单信息
     */
    public function preUpdate(ScoreOrder $object, PreUpdateEventArgs $eventArgs): void
    {
        if ('test' === $this->environment) {
            return;
        }
        $changeSet = $eventArgs->getEntityChangeSet();

        if (isset($changeSet['state']) && ScoreOrderState::DONE === $object->getState()) {
            $this->handleOrderCompletion($object);

            return;
        }

        if (isset($changeSet['modifyPriceReason'])) {
            $this->handlePriceModification($object);

            return;
        }
    }

    private function handleOrderCompletion(ScoreOrder $object): void
    {
        $requestJson = $this->buildCompletionRequestJson($object);

        $this->executeApiCall(
            $object,
            '完结支付分订单',
            fn ($builder) => $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/complete")->post(['json' => $requestJson]),
            $requestJson
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCompletionRequestJson(ScoreOrder $object): array
    {
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

        if (null !== $object->getEndLocation()) {
            $requestJson['location'] = [
                'end_location' => $object->getEndLocation(),
            ];
        }

        return $this->addPostPaymentsAndDiscounts($requestJson, $object);
    }

    private function handlePriceModification(ScoreOrder $object): void
    {
        $requestJson = [
            'out_order_no' => $object->getOutTradeNo(),
            'appid' => $object->getAppId(),
            'service_id' => $object->getServiceId(),
            'total_amount' => $object->getTotalAmount(),
            'reason' => $object->getModifyPriceReason(),
        ];

        $requestJson = $this->addPostPaymentsAndDiscounts($requestJson, $object);

        $this->executeApiCall(
            $object,
            '修改订单金额',
            fn ($builder) => $builder->chain("v3/payscore/serviceorder/{$object->getOutTradeNo()}/modify")->post(['json' => $requestJson]),
            $requestJson
        );
    }
}
