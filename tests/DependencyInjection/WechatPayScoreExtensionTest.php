<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatPayScoreBundle\DependencyInjection\WechatPayScoreExtension;

class WechatPayScoreExtensionTest extends TestCase
{
    private WechatPayScoreExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatPayScoreExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadServicesConfiguration(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证服务是否被加载（使用完整的类名作为服务ID）
        $this->assertTrue($this->container->has('WechatPayScoreBundle\Repository\ScoreOrderRepository'));
        $this->assertTrue($this->container->has('WechatPayScoreBundle\Repository\PostDiscountRepository'));
        $this->assertTrue($this->container->has('WechatPayScoreBundle\Repository\PostPaymentRepository'));
    }

    public function testLoadRegistersEventSubscriber(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证事件订阅者是否被注册（使用完整的类名作为服务ID）
        $this->assertTrue($this->container->has('WechatPayScoreBundle\EventSubscriber\ScoreOrderListener'));
    }
}