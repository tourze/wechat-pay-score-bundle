<?php

namespace WechatPayScoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\ScoreOrder;

class PostDiscountTest extends TestCase
{
    private PostDiscount $postDiscount;

    protected function setUp(): void
    {
        $this->postDiscount = new PostDiscount();
    }

    /**
     * 测试名称属性的getter和setter
     */
    public function testNameAccessors(): void
    {
        $this->assertNull($this->postDiscount->getName());

        $name = '优惠券';
        $this->postDiscount->setName($name);
        $this->assertSame($name, $this->postDiscount->getName());
    }

    /**
     * 测试描述属性的getter和setter
     */
    public function testDescriptionAccessors(): void
    {
        $this->assertNull($this->postDiscount->getDescription());

        $description = '满100减10优惠券';
        $this->postDiscount->setDescription($description);
        $this->assertSame($description, $this->postDiscount->getDescription());
    }

    /**
     * 测试数量属性的getter和setter
     */
    public function testCountAccessors(): void
    {
        $this->assertNull($this->postDiscount->getCount());

        $count = 2;
        $this->postDiscount->setCount($count);
        $this->assertSame($count, $this->postDiscount->getCount());

        // 测试设置null
        $this->postDiscount->setCount(null);
        $this->assertNull($this->postDiscount->getCount());
    }

    /**
     * 测试创建时间属性的getter和setter
     */
    public function testCreateTimeAccessors(): void
    {
        $this->assertNull($this->postDiscount->getCreateTime());

        $dateTime = new \DateTime();
        $this->postDiscount->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->postDiscount->getCreateTime());
    }

    /**
     * 测试更新时间属性的getter和setter
     */
    public function testUpdateTimeAccessors(): void
    {
        $this->assertNull($this->postDiscount->getUpdateTime());

        $dateTime = new \DateTime();
        $this->postDiscount->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->postDiscount->getUpdateTime());
    }

    /**
     * 测试创建人属性的getter和setter
     */
    public function testCreatedByAccessors(): void
    {
        $this->assertNull($this->postDiscount->getCreatedBy());

        $createdBy = 'admin';
        $this->postDiscount->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $this->postDiscount->getCreatedBy());
    }

    /**
     * 测试更新人属性的getter和setter
     */
    public function testUpdatedByAccessors(): void
    {
        $this->assertNull($this->postDiscount->getUpdatedBy());

        $updatedBy = 'admin';
        $this->postDiscount->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $this->postDiscount->getUpdatedBy());
    }

    /**
     * 测试与ScoreOrder的关联
     */
    public function testScoreOrderAssociation(): void
    {
        $this->assertNull($this->postDiscount->getScoreOrder());

        $scoreOrder = new ScoreOrder();
        $this->postDiscount->setScoreOrder($scoreOrder);
        $this->assertSame($scoreOrder, $this->postDiscount->getScoreOrder());
    }

    /**
     * 测试retrievePlainArray方法，确保返回的数组包含正确的字段
     */
    public function testRetrievePlainArray(): void
    {
        // 设置测试数据
        $this->postDiscount->setName('满减券');
        $this->postDiscount->setDescription('满100减10');
        $this->postDiscount->setCount(2);

        $plainArray = $this->postDiscount->retrievePlainArray();

        // 验证返回结构
        $this->assertIsArray($plainArray);
        $this->assertArrayHasKey('name', $plainArray);
        $this->assertArrayHasKey('description', $plainArray);
        $this->assertArrayHasKey('count', $plainArray);

        // 验证返回值
        $this->assertSame('满减券', $plainArray['name']);
        $this->assertSame('满100减10', $plainArray['description']);
        $this->assertSame(2, $plainArray['count']);
    }

    /**
     * 测试retrievePlainArray方法处理空值的情况
     */
    public function testRetrievePlainArray_withNullCount(): void
    {
        // 设置部分测试数据，保持count为null
        $this->postDiscount->setName('满减券');
        $this->postDiscount->setDescription('满100减10');
        // 不设置count，保持为null

        $plainArray = $this->postDiscount->retrievePlainArray();

        // 验证count被正确转换为整数0
        $this->assertSame(0, $plainArray['count']);
    }
}
