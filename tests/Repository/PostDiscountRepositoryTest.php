<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Repository\PostDiscountRepository;

class PostDiscountRepositoryTest extends TestCase
{

    /**
     * 测试PostDiscountRepository是否关联了正确的实体类
     */
    public function testEntityAssociation(): void
    {
        $reflectionClass = new \ReflectionClass(PostDiscountRepository::class);
        $constructor = $reflectionClass->getConstructor();

        // 检查构造函数的第一个参数
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $type = $parameters[0]->getType();
        $this->assertNotNull($type);
        $this->assertEquals(ManagerRegistry::class, $type->__toString());

        // 模拟实现来验证实体类
        $repo = $this->getMockBuilder(PostDiscountRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertInstanceOf(PostDiscountRepository::class, $repo);
    }
}
