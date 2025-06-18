<?php

namespace WechatPayScoreBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Enum\ScoreOrderState;

class ScoreOrderStateTest extends TestCase
{
    /**
     * 测试枚举值是否正确
     */
    public function testEnumValues(): void
    {
        $this->assertSame('CREATED', ScoreOrderState::CREATED->value);
        $this->assertSame('DOING', ScoreOrderState::DOING->value);
        $this->assertSame('DONE', ScoreOrderState::DONE->value);
        $this->assertSame('REVOKED', ScoreOrderState::REVOKED->value);
        $this->assertSame('EXPIRED', ScoreOrderState::EXPIRED->value);
    }

    /**
     * 测试getLabel方法
     */
    public function testGetLabel(): void
    {
        $this->assertSame('已创建', ScoreOrderState::CREATED->getLabel());
        $this->assertSame('进行中', ScoreOrderState::DOING->getLabel());
        $this->assertSame('已完成', ScoreOrderState::DONE->getLabel());
        $this->assertSame('取消服务', ScoreOrderState::REVOKED->getLabel());
        $this->assertSame('已失效', ScoreOrderState::EXPIRED->getLabel());
    }

    /**
     * 测试枚举列举所有案例
     */
    public function testCases(): void
    {
        $cases = ScoreOrderState::cases();
        $this->assertCount(5, $cases);
        $this->assertContains(ScoreOrderState::CREATED, $cases);
        $this->assertContains(ScoreOrderState::DOING, $cases);
        $this->assertContains(ScoreOrderState::DONE, $cases);
        $this->assertContains(ScoreOrderState::REVOKED, $cases);
        $this->assertContains(ScoreOrderState::EXPIRED, $cases);
    }
}
