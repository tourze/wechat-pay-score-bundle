<?php

namespace WechatPayScoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * @internal
 */
#[CoversClass(PostPayment::class)]
final class PostPaymentTest extends AbstractEntityTestCase
{
    private PostPayment $postPayment;

    protected function setUp(): void
    {
        $this->postPayment = new PostPayment();
    }

    protected function createEntity(): object
    {
        return new PostPayment();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试服务名称'];
        yield 'amount' => ['amount', 1000];
        yield 'description' => ['description', '测试服务描述'];
        yield 'count' => ['count', 1];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_user'];
    }

    /**
     * 测试名称属性的getter和setter
     */
    public function testNameAccessors(): void
    {
        $this->assertNull($this->postPayment->getName());

        $name = '会员服务';
        $this->postPayment->setName($name);
        $this->assertSame($name, $this->postPayment->getName());

        // 测试nullable
        $this->postPayment->setName(null);
        $this->assertNull($this->postPayment->getName());
    }

    /**
     * 测试金额属性的getter和setter
     */
    public function testAmountAccessors(): void
    {
        $this->assertNull($this->postPayment->getAmount());

        $amount = 1000; // 10元，单位为分
        $this->postPayment->setAmount($amount);
        $this->assertSame($amount, $this->postPayment->getAmount());
    }

    /**
     * 测试描述属性的getter和setter
     */
    public function testDescriptionAccessors(): void
    {
        $this->assertNull($this->postPayment->getDescription());

        $description = '月度会员费用';
        $this->postPayment->setDescription($description);
        $this->assertSame($description, $this->postPayment->getDescription());

        // 测试nullable
        $this->postPayment->setDescription(null);
        $this->assertNull($this->postPayment->getDescription());
    }

    /**
     * 测试数量属性的getter和setter
     */
    public function testCountAccessors(): void
    {
        $this->assertNull($this->postPayment->getCount());

        $count = 2;
        $this->postPayment->setCount($count);
        $this->assertSame($count, $this->postPayment->getCount());

        // 测试nullable
        $this->postPayment->setCount(null);
        $this->assertNull($this->postPayment->getCount());
    }

    /**
     * 测试创建时间属性的getter和setter
     */
    public function testCreateTimeAccessors(): void
    {
        $this->assertNull($this->postPayment->getCreateTime());

        $dateTime = new \DateTimeImmutable();
        $this->postPayment->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->postPayment->getCreateTime());
    }

    /**
     * 测试更新时间属性的getter和setter
     */
    public function testUpdateTimeAccessors(): void
    {
        $this->assertNull($this->postPayment->getUpdateTime());

        $dateTime = new \DateTimeImmutable();
        $this->postPayment->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->postPayment->getUpdateTime());
    }

    /**
     * 测试创建人属性的getter和setter
     */
    public function testCreatedByAccessors(): void
    {
        $this->assertNull($this->postPayment->getCreatedBy());

        $createdBy = 'admin';
        $this->postPayment->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $this->postPayment->getCreatedBy());
    }

    /**
     * 测试更新人属性的getter和setter
     */
    public function testUpdatedByAccessors(): void
    {
        $this->assertNull($this->postPayment->getUpdatedBy());

        $updatedBy = 'admin';
        $this->postPayment->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $this->postPayment->getUpdatedBy());
    }

    /**
     * 测试与ScoreOrder的关联
     */
    public function testScoreOrderAssociation(): void
    {
        $this->assertNull($this->postPayment->getScoreOrder());

        $scoreOrder = new ScoreOrder();
        $this->postPayment->setScoreOrder($scoreOrder);
        $this->assertSame($scoreOrder, $this->postPayment->getScoreOrder());
    }

    /**
     * 测试retrievePlainArray方法，确保返回的数组包含正确的字段
     */
    public function testRetrievePlainArray(): void
    {
        // 设置测试数据
        $this->postPayment->setName('会员服务');
        $this->postPayment->setAmount(1000);
        $this->postPayment->setDescription('月度会员费用');
        $this->postPayment->setCount(1);

        $plainArray = $this->postPayment->retrievePlainArray();

        // 验证返回结构
        $this->assertArrayHasKey('name', $plainArray);
        $this->assertArrayHasKey('amount', $plainArray);
        $this->assertArrayHasKey('description', $plainArray);
        $this->assertArrayHasKey('count', $plainArray);

        // 验证返回值
        $this->assertSame('会员服务', $plainArray['name']);
        $this->assertSame(1000, $plainArray['amount']);
        $this->assertSame('月度会员费用', $plainArray['description']);
        $this->assertSame(1, $plainArray['count']);
    }

    /**
     * 测试retrievePlainArray方法处理null值的情况
     */
    public function testRetrievePlainArrayWithNullValues(): void
    {
        // 设置部分测试数据，保持一些字段为null
        $this->postPayment->setAmount(1000); // 必须设置，因为不允许为null
        // name和description保持为null
        // count保持为null

        $plainArray = $this->postPayment->retrievePlainArray();

        // 验证null值被正确处理
        $this->assertNull($plainArray['name']);
        $this->assertSame(1000, $plainArray['amount']);
        $this->assertSame('', $plainArray['description']); // 验证null被转为空字符串
        $this->assertSame(0, $plainArray['count']); // 验证null被转为整数0
    }
}
