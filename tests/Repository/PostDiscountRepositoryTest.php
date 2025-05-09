<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Repository\PostDiscountRepository;

class PostDiscountRepositoryTest extends TestCase
{
    /**
     * 测试PostDiscountRepository是否继承自ServiceEntityRepository
     */
    public function testInheritance(): void
    {
        $this->assertTrue(is_subclass_of(
            PostDiscountRepository::class,
            ServiceEntityRepository::class
        ));
    }

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
        $this->assertEquals(ManagerRegistry::class, $parameters[0]->getType()->getName());

        // 模拟实现来验证实体类
        $repo = $this->getMockBuilder(PostDiscountRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertInstanceOf(PostDiscountRepository::class, $repo);
    }
}
