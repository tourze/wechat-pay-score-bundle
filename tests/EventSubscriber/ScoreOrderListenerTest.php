<?php

namespace WechatPayScoreBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatPayBundle\Entity\Merchant;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use WechatPayScoreBundle\EventSubscriber\ScoreOrderListener;

/**
 * @internal
 */
#[CoversClass(ScoreOrderListener::class)]
#[RunTestsInSeparateProcesses]
final class ScoreOrderListenerTest extends AbstractIntegrationTestCase
{
    private ScoreOrderListener $listener;

    private ScoreOrder $scoreOrder;

    protected function onSetUp(): void
    {
        // 从容器获取监听器实例
        $this->listener = self::getService(ScoreOrderListener::class);

        // 创建测试用的ScoreOrder实例
        $this->scoreOrder = new ScoreOrder();
        $this->prepareScoreOrder();
    }

    /**
     * 准备ScoreOrder测试数据
     */
    private function prepareScoreOrder(): void
    {
        // 设置基本订单信息
        $this->scoreOrder->setOutTradeNo('TEST123456');
        $this->scoreOrder->setAppId('wx1234567890');
        $this->scoreOrder->setServiceId('500001');
        $this->scoreOrder->setServiceIntroduction('信用借还服务');
        $this->scoreOrder->setStartTime('20230101120000');
        $this->scoreOrder->setStartTimeRemark('服务开始时间');
        $this->scoreOrder->setRiskFundName('DEPOSIT');
        $this->scoreOrder->setRiskFundAmount(1000);
        $this->scoreOrder->setRiskFundDescription('押金');
        $this->scoreOrder->setNotifyUrl('https://example.com/notify');

        // 创建并添加后付费项目
        $postPayment = new PostPayment();
        $postPayment->setName('会员服务');
        $postPayment->setAmount(1000);
        $postPayment->setDescription('月度会员费用');
        $postPayment->setCount(1);
        $this->scoreOrder->addScorePostPayment($postPayment);

        // 创建并添加后付费优惠
        $postDiscount = new PostDiscount();
        $postDiscount->setName('优惠券');
        $postDiscount->setDescription('满100减10');
        $postDiscount->setCount(1);
        $this->scoreOrder->addPostDiscount($postDiscount);

        // 关联商户
        $merchant = new Merchant();
        $this->scoreOrder->setMerchant($merchant);
    }

    /**
     * 测试preRemove方法在测试环境中的行为
     */
    public function testPreRemoveInTestEnvironment(): void
    {
        // 设置订单状态为DONE，此状态在非测试环境不允许取消
        $this->scoreOrder->setState(ScoreOrderState::DONE);

        // 在测试环境中，preRemove 应该直接返回，不执行任何操作
        // 即使状态为DONE（通常不允许取消），也不会抛出异常
        $this->listener->preRemove($this->scoreOrder);

        // 验证订单状态没有被改变
        $this->assertSame(ScoreOrderState::DONE, $this->scoreOrder->getState());
    }

    /**
     * 测试preUpdate方法在测试环境中的行为
     */
    public function testPreUpdateInTestEnvironment(): void
    {
        // 设置订单的初始状态为CREATED
        $this->scoreOrder->setState(ScoreOrderState::CREATED);

        // 模拟EntityChangeSet，包含state变更为DONE
        $changeSet = ['state' => [ScoreOrderState::CREATED, ScoreOrderState::DONE]];

        // 模拟PreUpdateEventArgs
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);
        $eventArgs->method('getEntityChangeSet')
            ->willReturn($changeSet)
        ;

        // 在测试环境中，preUpdate 应该直接返回，不执行任何操作
        // 即使状态变更为DONE（通常会触发完结操作），也不会调用远程API
        $this->listener->preUpdate($this->scoreOrder, $eventArgs);

        // 验证订单状态没有被改变（因为测试环境中不会同步到远程）
        $this->assertSame(ScoreOrderState::CREATED, $this->scoreOrder->getState());
    }

    /**
     * 测试postLoad方法在测试环境中的行为
     */
    public function testPostLoadInTestEnvironment(): void
    {
        // 设置订单的初始状态
        $this->scoreOrder->setState(ScoreOrderState::CREATED);

        // PostLoadEventArgs 是 final 类，创建真实实例
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->never())
            ->method('persist')
        ;
        $objectManager->expects($this->never())
            ->method('flush')
        ;

        $eventArgs = new PostLoadEventArgs($this->scoreOrder, $objectManager);

        // 在测试环境中，postLoad 应该直接返回，不执行任何操作
        // 不会调用远程API同步状态，也不会触发持久化操作
        $this->listener->postLoad($this->scoreOrder, $eventArgs);

        // 验证订单状态没有被改变
        $this->assertSame(ScoreOrderState::CREATED, $this->scoreOrder->getState());
    }

    /**
     * 测试prePersist方法在测试环境中的行为
     */
    public function testPrePersistInTestEnvironment(): void
    {
        // 设置订单的初始状态为null
        $this->scoreOrder->setState(null);

        // 在测试环境中，prePersist 应该直接返回，不执行任何操作
        // 不会调用远程API创建订单，也不会修改订单状态
        $this->listener->prePersist($this->scoreOrder);

        // 验证订单状态仍然为null（没有被远程API修改）
        $this->assertNull($this->scoreOrder->getState());
        $this->assertNull($this->scoreOrder->getOrderId());
        $this->assertNull($this->scoreOrder->getPackage());
    }
}
