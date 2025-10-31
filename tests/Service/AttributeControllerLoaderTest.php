<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouteCollection;
use WechatPayScoreBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends WebTestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        // No specific setup needed for this test
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $this->loader = new AttributeControllerLoader();
        $result = $this->loader->load(null);

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $this->loader = new AttributeControllerLoader();
        $result = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    public function testSupportsAlwaysReturnsFalse(): void
    {
        $this->loader = new AttributeControllerLoader();
        $this->assertFalse($this->loader->supports('any_resource'));
        $this->assertFalse($this->loader->supports('any_resource', 'any_type'));
        $this->assertFalse($this->loader->supports(null));
    }

    public function testAutoloadRegistersCallbackControllerRoutes(): void
    {
        $this->loader = new AttributeControllerLoader();
        $collection = $this->loader->autoload();

        // 验证是否包含回调路由
        $route = $collection->get('wechat_payment_score_order_success_callback');
        $this->assertNotNull($route);
    }
}
