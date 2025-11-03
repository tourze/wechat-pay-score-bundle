<?php

declare(strict_types=1);

namespace WechatPayScoreBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use WechatPayBundle\WechatPayBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatPayScoreBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            WechatPayBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
