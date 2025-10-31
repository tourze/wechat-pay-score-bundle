<?php

namespace WechatPayScoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatPayScoreBundle\Enum\ScoreOrderState;

/**
 * @internal
 */
#[CoversClass(ScoreOrderState::class)]
final class ScoreOrderStateTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确
     */
    #[TestWith(['CREATED', 'CREATED', '已创建'])]
    #[TestWith(['DOING', 'DOING', '进行中'])]
    #[TestWith(['DONE', 'DONE', '已完成'])]
    #[TestWith(['REVOKED', 'REVOKED', '取消服务'])]
    #[TestWith(['EXPIRED', 'EXPIRED', '已失效'])]
    public function testEnumValuesAndLabels(string $enumName, string $expectedValue, string $expectedLabel): void
    {
        $enum = ScoreOrderState::from($expectedValue);
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    /**
     * 测试 tryFrom() 方法的有效输入
     */
    public function testTryFromWithValidValue(): void
    {
        $result = ScoreOrderState::tryFrom('CREATED');
        $this->assertInstanceOf(ScoreOrderState::class, $result);
        $this->assertSame(ScoreOrderState::CREATED, $result);
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

    /**
     * 测试toArray方法
     */
    public function testToArray(): void
    {
        $array = ScoreOrderState::CREATED->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('CREATED', $array['value']);
        $this->assertSame('已创建', $array['label']);

        // 测试其他枚举值
        $doingArray = ScoreOrderState::DOING->toArray();
        $this->assertSame('DOING', $doingArray['value']);
        $this->assertSame('进行中', $doingArray['label']);
    }

    /**
     * 测试toSelectItem方法的具体实现
     */
    public function testToSelectItemImplementation(): void
    {
        $selectItem = ScoreOrderState::CREATED->toSelectItem();
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertSame('CREATED', $selectItem['value']);
        $this->assertSame('已创建', $selectItem['label']);
    }

    /**
     * 测试getBadgeType方法
     */
    #[TestWith([ScoreOrderState::CREATED, 'info'])]
    #[TestWith([ScoreOrderState::DOING, 'warning'])]
    #[TestWith([ScoreOrderState::DONE, 'success'])]
    #[TestWith([ScoreOrderState::REVOKED, 'danger'])]
    #[TestWith([ScoreOrderState::EXPIRED, 'secondary'])]
    public function testGetBadgeType(ScoreOrderState $state, string $expectedBadgeType): void
    {
        $this->assertSame($expectedBadgeType, $state->getBadgeType());
    }

    /**
     * 测试getBadge方法
     */
    public function testGetBadge(): void
    {
        // getBadge方法应该返回与getLabel相同的值
        $this->assertSame('已创建', ScoreOrderState::CREATED->getBadge());
        $this->assertSame('进行中', ScoreOrderState::DOING->getBadge());
        $this->assertSame('已完成', ScoreOrderState::DONE->getBadge());
        $this->assertSame('取消服务', ScoreOrderState::REVOKED->getBadge());
        $this->assertSame('已失效', ScoreOrderState::EXPIRED->getBadge());
    }

    /**
     * 测试枚举值的唯一性
     */
    public function testValueUniqueness(): void
    {
        $cases = ScoreOrderState::cases();
        $values = array_map(fn ($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values should be unique');
    }

    /**
     * 测试枚举标签的唯一性
     */
    public function testLabelUniqueness(): void
    {
        $cases = ScoreOrderState::cases();
        $labels = array_map(fn ($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels should be unique');
    }
}
