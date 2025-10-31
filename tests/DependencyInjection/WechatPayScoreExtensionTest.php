<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatPayScoreBundle\DependencyInjection\WechatPayScoreExtension;

/**
 * @internal
 */
#[CoversClass(WechatPayScoreExtension::class)]
final class WechatPayScoreExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatPayScoreExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new WechatPayScoreExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testLoadRegistersEventSubscriber(): void
    {
        $this->extension->load([], $this->container);

        // 验证事件订阅者是否被注册（使用完整的类名作为服务ID）
        $this->assertTrue($this->container->has('WechatPayScoreBundle\EventSubscriber\ScoreOrderListener'));
    }
}
