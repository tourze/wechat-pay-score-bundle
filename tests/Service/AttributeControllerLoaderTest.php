<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use WechatPayScoreBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $result = $this->loader->load(null);
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $result = $this->loader->autoload();
        
        $this->assertInstanceOf(RouteCollection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    public function testSupportsAlwaysReturnsFalse(): void
    {
        $this->assertFalse($this->loader->supports('any_resource'));
        $this->assertFalse($this->loader->supports('any_resource', 'any_type'));
        $this->assertFalse($this->loader->supports(null));
    }

    public function testAutoloadRegistersCallbackControllerRoutes(): void
    {
        $collection = $this->loader->autoload();
        
        // 验证是否包含回调路由
        $route = $collection->get('wechat_payment_score_order_success_callback');
        $this->assertNotNull($route);
    }
}