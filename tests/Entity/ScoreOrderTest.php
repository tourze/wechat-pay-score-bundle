<?php

namespace WechatPayScoreBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;

/**
 * @internal
 */
#[CoversClass(ScoreOrder::class)]
final class ScoreOrderTest extends AbstractEntityTestCase
{
    private ScoreOrder $scoreOrder;

    protected function setUp(): void
    {
        $this->scoreOrder = new ScoreOrder();
    }

    protected function createEntity(): object
    {
        return new ScoreOrder();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'appId' => ['appId', 'wx1234567890'];
        yield 'serviceId' => ['serviceId', '500001'];
        yield 'outTradeNo' => ['outTradeNo', 'TEST123456'];
        yield 'serviceIntroduction' => ['serviceIntroduction', '测试服务介绍'];
        yield 'startTime' => ['startTime', '20230101120000'];
        yield 'startTimeRemark' => ['startTimeRemark', '服务开始时间'];
        yield 'endTime' => ['endTime', '20230102120000'];
        yield 'endTimeRemark' => ['endTimeRemark', '服务结束时间'];
        yield 'startLocation' => ['startLocation', '北京市海淀区'];
        yield 'endLocation' => ['endLocation', '北京市朝阳区'];
        yield 'riskFundName' => ['riskFundName', 'DEPOSIT'];
        yield 'riskFundAmount' => ['riskFundAmount', 1000];
        yield 'riskFundDescription' => ['riskFundDescription', '押金'];
        yield 'attach' => ['attach', '附加数据'];
        yield 'notifyUrl' => ['notifyUrl', 'https://example.com/notify'];
        yield 'openId' => ['openId', 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'];
        yield 'needUserConfirm' => ['needUserConfirm', true];
        yield 'state' => ['state', ScoreOrderState::CREATED];
        yield 'stateDescription' => ['stateDescription', '订单已创建'];
        yield 'orderId' => ['orderId', '15646546545165651651'];
        yield 'package' => ['package', 'prepay_id=wx201410272009395522657a690389285100'];
        yield 'totalAmount' => ['totalAmount', 2000];
        yield 'needCollection' => ['needCollection', true];
        yield 'collection' => ['collection', ['state' => 'USER_PAYING']];
        yield 'cancelReason' => ['cancelReason', '用户取消服务'];
        yield 'modifyPriceReason' => ['modifyPriceReason', '订单金额调整'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    /**
     * 测试构造函数是否正确初始化集合
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(Collection::class, $this->scoreOrder->getPostPayments());
        $this->assertInstanceOf(Collection::class, $this->scoreOrder->getPostDiscounts());
        $this->assertCount(0, $this->scoreOrder->getPostPayments());
        $this->assertCount(0, $this->scoreOrder->getPostDiscounts());
    }

    /**
     * 测试AppId属性的getter和setter
     */
    public function testAppIdAccessors(): void
    {
        $this->assertNull($this->scoreOrder->getAppId());

        $appId = 'wx1234567890';
        $this->scoreOrder->setAppId($appId);
        $this->assertSame($appId, $this->scoreOrder->getAppId());
    }

    /**
     * 测试ServiceId属性的getter和setter
     */
    public function testServiceIdAccessors(): void
    {
        $this->assertNull($this->scoreOrder->getServiceId());

        $serviceId = '500001';
        $this->scoreOrder->setServiceId($serviceId);
        $this->assertSame($serviceId, $this->scoreOrder->getServiceId());
    }

    /**
     * 测试OutTradeNo属性的getter和setter
     */
    public function testOutTradeNoAccessors(): void
    {
        $this->assertNull($this->scoreOrder->getOutTradeNo());

        $outTradeNo = '1234323JKHDFE1243252';
        $this->scoreOrder->setOutTradeNo($outTradeNo);
        $this->assertSame($outTradeNo, $this->scoreOrder->getOutTradeNo());
    }

    /**
     * 测试ServiceIntroduction属性的getter和setter
     */
    public function testServiceIntroductionAccessors(): void
    {
        $this->assertNull($this->scoreOrder->getServiceIntroduction());

        $serviceIntroduction = '信用借还服务';
        $this->scoreOrder->setServiceIntroduction($serviceIntroduction);
        $this->assertSame($serviceIntroduction, $this->scoreOrder->getServiceIntroduction());
    }

    /**
     * 测试时间相关字段的getter和setter
     */
    public function testTimeRelatedAccessors(): void
    {
        // StartTime
        $this->assertNull($this->scoreOrder->getStartTime());
        $startTime = '20231001120000';
        $this->scoreOrder->setStartTime($startTime);
        $this->assertSame($startTime, $this->scoreOrder->getStartTime());

        // StartTimeRemark
        $this->assertNull($this->scoreOrder->getStartTimeRemark());
        $startTimeRemark = '服务开始时间';
        $this->scoreOrder->setStartTimeRemark($startTimeRemark);
        $this->assertSame($startTimeRemark, $this->scoreOrder->getStartTimeRemark());

        // EndTime
        $this->assertNull($this->scoreOrder->getEndTime());
        $endTime = '20231002120000';
        $this->scoreOrder->setEndTime($endTime);
        $this->assertSame($endTime, $this->scoreOrder->getEndTime());

        // EndTimeRemark
        $this->assertNull($this->scoreOrder->getEndTimeRemark());
        $endTimeRemark = '服务结束时间';
        $this->scoreOrder->setEndTimeRemark($endTimeRemark);
        $this->assertSame($endTimeRemark, $this->scoreOrder->getEndTimeRemark());
    }

    /**
     * 测试位置相关字段的getter和setter
     */
    public function testLocationRelatedAccessors(): void
    {
        // StartLocation
        $this->assertNull($this->scoreOrder->getStartLocation());
        $startLocation = '北京市海淀区';
        $this->scoreOrder->setStartLocation($startLocation);
        $this->assertSame($startLocation, $this->scoreOrder->getStartLocation());

        // EndLocation
        $this->assertNull($this->scoreOrder->getEndLocation());
        $endLocation = '北京市朝阳区';
        $this->scoreOrder->setEndLocation($endLocation);
        $this->assertSame($endLocation, $this->scoreOrder->getEndLocation());
    }

    /**
     * 测试风险基金相关字段的getter和setter
     */
    public function testRiskFundRelatedAccessors(): void
    {
        // RiskFundName
        $this->assertNull($this->scoreOrder->getRiskFundName());
        $riskFundName = 'DEPOSIT';
        $this->scoreOrder->setRiskFundName($riskFundName);
        $this->assertSame($riskFundName, $this->scoreOrder->getRiskFundName());

        // RiskFundAmount
        $this->assertNull($this->scoreOrder->getRiskFundAmount());
        $riskFundAmount = 1000; // 10元，单位为分
        $this->scoreOrder->setRiskFundAmount($riskFundAmount);
        $this->assertSame($riskFundAmount, $this->scoreOrder->getRiskFundAmount());

        // RiskFundDescription
        $this->assertNull($this->scoreOrder->getRiskFundDescription());
        $riskFundDescription = '押金';
        $this->scoreOrder->setRiskFundDescription($riskFundDescription);
        $this->assertSame($riskFundDescription, $this->scoreOrder->getRiskFundDescription());
    }

    /**
     * 测试其他基本字段的getter和setter
     */
    public function testOtherBasicAccessors(): void
    {
        // Attach
        $this->assertNull($this->scoreOrder->getAttach());
        $attach = '附加数据';
        $this->scoreOrder->setAttach($attach);
        $this->assertSame($attach, $this->scoreOrder->getAttach());

        // NotifyUrl
        $this->assertNull($this->scoreOrder->getNotifyUrl());
        $notifyUrl = 'https://example.com/notify';
        $this->scoreOrder->setNotifyUrl($notifyUrl);
        $this->assertSame($notifyUrl, $this->scoreOrder->getNotifyUrl());

        // OpenId
        $this->assertNull($this->scoreOrder->getOpenId());
        $openId = 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o';
        $this->scoreOrder->setOpenId($openId);
        $this->assertSame($openId, $this->scoreOrder->getOpenId());

        // NeedUserConfirm
        $this->assertNull($this->scoreOrder->isNeedUserConfirm());
        $needUserConfirm = true;
        $this->scoreOrder->setNeedUserConfirm($needUserConfirm);
        $this->assertSame($needUserConfirm, $this->scoreOrder->isNeedUserConfirm());
    }

    /**
     * 测试状态相关字段的getter和setter
     */
    public function testStateRelatedAccessors(): void
    {
        // State
        $this->assertNull($this->scoreOrder->getState());
        $state = ScoreOrderState::CREATED;
        $this->scoreOrder->setState($state);
        $this->assertSame($state, $this->scoreOrder->getState());

        // StateDescription
        $this->assertNull($this->scoreOrder->getStateDescription());
        $stateDescription = '订单已创建';
        $this->scoreOrder->setStateDescription($stateDescription);
        $this->assertSame($stateDescription, $this->scoreOrder->getStateDescription());
    }

    /**
     * 测试订单信息相关字段的getter和setter
     */
    public function testOrderInfoAccessors(): void
    {
        // OrderId
        $this->assertNull($this->scoreOrder->getOrderId());
        $orderId = '15646546545165651651';
        $this->scoreOrder->setOrderId($orderId);
        $this->assertSame($orderId, $this->scoreOrder->getOrderId());

        // Package
        $this->assertNull($this->scoreOrder->getPackage());
        $package = 'prepay_id=wx201410272009395522657a690389285100';
        $this->scoreOrder->setPackage($package);
        $this->assertSame($package, $this->scoreOrder->getPackage());
    }

    /**
     * 测试金额和收款相关字段的getter和setter
     */
    public function testAmountRelatedAccessors(): void
    {
        // TotalAmount
        $this->assertNull($this->scoreOrder->getTotalAmount());
        $totalAmount = 2000; // 20元，单位为分
        $this->scoreOrder->setTotalAmount($totalAmount);
        $this->assertSame($totalAmount, $this->scoreOrder->getTotalAmount());

        // NeedCollection
        $this->assertNull($this->scoreOrder->isNeedCollection());
        $needCollection = true;
        $this->scoreOrder->setNeedCollection($needCollection);
        $this->assertSame($needCollection, $this->scoreOrder->isNeedCollection());

        // Collection
        $this->assertNull($this->scoreOrder->getCollection());
        $collection = ['state' => 'USER_PAYING'];
        $this->scoreOrder->setCollection($collection);
        $this->assertSame($collection, $this->scoreOrder->getCollection());
    }

    /**
     * 测试原因相关字段的getter和setter
     */
    public function testReasonAccessors(): void
    {
        // CancelReason
        $this->assertNull($this->scoreOrder->getCancelReason());
        $cancelReason = '用户取消服务';
        $this->scoreOrder->setCancelReason($cancelReason);
        $this->assertSame($cancelReason, $this->scoreOrder->getCancelReason());

        // ModifyPriceReason
        $this->assertNull($this->scoreOrder->getModifyPriceReason());
        $modifyPriceReason = '订单金额调整';
        $this->scoreOrder->setModifyPriceReason($modifyPriceReason);
        $this->assertSame($modifyPriceReason, $this->scoreOrder->getModifyPriceReason());
    }

    /**
     * 测试时间戳字段的getter和setter
     */
    public function testTimestampAccessors(): void
    {
        // CreateTime
        $this->assertNull($this->scoreOrder->getCreateTime());
        $createTime = new \DateTimeImmutable();
        $this->scoreOrder->setCreateTime($createTime);
        $this->assertSame($createTime, $this->scoreOrder->getCreateTime());

        // UpdateTime
        $this->assertNull($this->scoreOrder->getUpdateTime());
        $updateTime = new \DateTimeImmutable();
        $this->scoreOrder->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->scoreOrder->getUpdateTime());
    }

    /**
     * 测试商户关联的getter和setter
     */
    public function testMerchantAccessors(): void
    {
        $this->assertNull($this->scoreOrder->getMerchant());

        $merchant = new Merchant();
        $this->scoreOrder->setMerchant($merchant);
        $this->assertSame($merchant, $this->scoreOrder->getMerchant());
    }

    /**
     * 测试PostPayment集合的操作方法
     */
    public function testPostPaymentCollectionMethods(): void
    {
        // 初始状态
        $this->assertCount(0, $this->scoreOrder->getPostPayments());

        // 添加一个PostPayment
        $postPayment = new PostPayment();
        $postPayment->setName('会员服务');
        $postPayment->setAmount(1000);

        $this->scoreOrder->addScorePostPayment($postPayment);
        $this->assertCount(1, $this->scoreOrder->getPostPayments());
        $this->assertSame($this->scoreOrder, $postPayment->getScoreOrder()); // 验证关联关系是否已建立

        // 移除这个PostPayment
        $this->scoreOrder->removePostPayment($postPayment);
        $this->assertCount(0, $this->scoreOrder->getPostPayments());
        $this->assertNull($postPayment->getScoreOrder()); // 验证关联关系是否已解除
    }

    /**
     * 测试PostDiscount集合的操作方法
     */
    public function testPostDiscountCollectionMethods(): void
    {
        // 初始状态
        $this->assertCount(0, $this->scoreOrder->getPostDiscounts());

        // 添加一个PostDiscount
        $postDiscount = new PostDiscount();
        $postDiscount->setName('优惠券');
        $postDiscount->setDescription('满100减10');

        $this->scoreOrder->addPostDiscount($postDiscount);
        $this->assertCount(1, $this->scoreOrder->getPostDiscounts());
        $this->assertSame($this->scoreOrder, $postDiscount->getScoreOrder()); // 验证关联关系是否已建立

        // 移除这个PostDiscount
        $this->scoreOrder->removePostDiscount($postDiscount);
        $this->assertCount(0, $this->scoreOrder->getPostDiscounts());
        $this->assertNull($postDiscount->getScoreOrder()); // 验证关联关系是否已解除
    }
}
