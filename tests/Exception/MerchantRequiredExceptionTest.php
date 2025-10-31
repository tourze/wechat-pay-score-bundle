<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatPayScoreBundle\Exception\MerchantRequiredException;

/**
 * @internal
 */
#[CoversClass(MerchantRequiredException::class)]
final class MerchantRequiredExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsInstanceOfInvalidArgumentException(): void
    {
        $exception = new MerchantRequiredException('Test message');
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testExceptionPreservesMessage(): void
    {
        $message = 'ScoreOrder must have a merchant';
        $exception = new MerchantRequiredException($message);
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionPreservesCode(): void
    {
        $code = 400;
        $exception = new MerchantRequiredException('Test message', $code);
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionPreservesOriginalException(): void
    {
        $originalException = new \RuntimeException('Original error');
        $exception = new MerchantRequiredException('Test message', 0, $originalException);
        $this->assertSame($originalException, $exception->getPrevious());
    }
}
