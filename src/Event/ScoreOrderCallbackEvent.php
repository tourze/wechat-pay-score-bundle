<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter6_1_22.shtml
 */
final class ScoreOrderCallbackEvent extends Event
{
    private string $eventType;

    /**
     * @var array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getResource(): array
    {
        return $this->resource;
    }

    /**
     * @param array<string, mixed> $resource
     */
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
