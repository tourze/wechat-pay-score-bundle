<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatPayScoreBundle\WechatPayScoreBundle;

/**
 * @internal
 */
#[CoversClass(WechatPayScoreBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatPayScoreBundleTest extends AbstractBundleTestCase
{
}
