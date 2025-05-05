<?php

namespace WechatPayScoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_22.shtml
 */
class ScoreOrderCallbackEvent extends Event
{
    private string $eventType;

    private array $resource = [];

    private ScoreOrder $scoreOrder;

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getResource(): array
    {
        return $this->resource;
    }

    public function setResource(array $resource): void
    {
        $this->resource = $resource;
    }

    public function getScoreOrder(): ScoreOrder
    {
        return $this->scoreOrder;
    }

    public function setScoreOrder(ScoreOrder $scoreOrder): void
    {
        $this->scoreOrder = $scoreOrder;
    }
}
