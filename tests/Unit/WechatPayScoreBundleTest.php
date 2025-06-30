<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use WechatPayScoreBundle\WechatPayScoreBundle;

class WechatPayScoreBundleTest extends TestCase
{
    public function testBundleExtendsSymfonyBundle(): void
    {
        $bundle = new WechatPayScoreBundle();
        
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
    
    public function testBundleImplementsBundleDependencyInterface(): void
    {
        $bundle = new WechatPayScoreBundle();
        
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }
    
    public function testGetBundleDependencies(): void
    {
        $dependencies = WechatPayScoreBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(\WechatPayBundle\WechatPayBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[\WechatPayBundle\WechatPayBundle::class]);
    }
}