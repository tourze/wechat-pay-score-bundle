<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Exception\ScoreOrderCancelException;

class ScoreOrderCancelExceptionTest extends TestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $exception = new ScoreOrderCancelException('Test message');
        
        $this->assertInstanceOf(ScoreOrderCancelException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
    
    public function testExceptionCanBeThrownWithCode(): void
    {
        $exception = new ScoreOrderCancelException('Test message', 123);
        
        $this->assertEquals(123, $exception->getCode());
    }
    
    public function testExceptionCanBeThrownWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ScoreOrderCancelException('Test message', 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
}