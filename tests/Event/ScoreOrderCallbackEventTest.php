<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;

class ScoreOrderCallbackEventTest extends TestCase
{
    private ScoreOrderCallbackEvent $event;

    protected function setUp(): void
    {
        $this->event = new ScoreOrderCallbackEvent();
    }

    public function testEventTypeGetterAndSetter(): void
    {
        $this->event->setEventType('TRANSACTION.SUCCESS');
        $this->assertEquals('TRANSACTION.SUCCESS', $this->event->getEventType());
    }

    public function testResourceGetterAndSetter(): void
    {
        $resource = [
            'out_trade_no' => 'test123',
            'amount' => 100,
            'status' => 'SUCCESS'
        ];
        
        $this->event->setResource($resource);
        $this->assertEquals($resource, $this->event->getResource());
    }

    public function testScoreOrderGetterAndSetter(): void
    {
        $scoreOrder = $this->createMock(ScoreOrder::class);
        
        $this->event->setScoreOrder($scoreOrder);
        $this->assertSame($scoreOrder, $this->event->getScoreOrder());
    }

    public function testResourceDefaultsToEmptyArray(): void
    {
        $event = new ScoreOrderCallbackEvent();
        $this->assertEquals([], $event->getResource());
    }
}