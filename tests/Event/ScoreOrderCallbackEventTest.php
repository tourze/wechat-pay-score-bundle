<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;

/**
 * @internal
 */
#[CoversClass(ScoreOrderCallbackEvent::class)]
final class ScoreOrderCallbackEventTest extends AbstractEventTestCase
{
    private ScoreOrderCallbackEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

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
            'status' => 'SUCCESS',
        ];

        $this->event->setResource($resource);
        $this->assertEquals($resource, $this->event->getResource());
    }

    public function testScoreOrderGetterAndSetter(): void
    {
        // 使用具体类 ScoreOrder 进行 mock：
        // 1) ScoreOrder 是实体类，没有对应的接口
        // 2) 这种使用是合理的，因为我们只需要测试 Event 类的 getter/setter 逻辑
        // 3) 暂无更好的替代方案，实体类本身设计为具体实现
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
