<?php

namespace WechatPayScoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class WechatPayScoreBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatPayBundle\WechatPayBundle::class => ['all' => true],
        ];
    }
}
