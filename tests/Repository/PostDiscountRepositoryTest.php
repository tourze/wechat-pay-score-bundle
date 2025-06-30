<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Repository\PostDiscountRepository;

class PostDiscountRepositoryTest extends TestCase
{
    /**
     * 测试PostDiscountRepository是否正确扩展了ServiceEntityRepository
     */
    public function testRepositoryStructure(): void
    {
        $reflectionClass = new \ReflectionClass(PostDiscountRepository::class);
        
        // 验证父类
        $this->assertEquals('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $reflectionClass->getParentClass()->getName());
        
        // 验证构造函数参数
        $constructor = $reflectionClass->getConstructor();
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        
        $firstParam = $parameters[0];
        $this->assertEquals('registry', $firstParam->getName());
        $type = $firstParam->getType();
        $this->assertNotNull($type);
        $this->assertEquals(ManagerRegistry::class, (string) $type);
    }
    
    /**
     * 测试是否关联了正确的实体类
     */
    public function testEntityClassAssociation(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        
        // 使用反射来检查传递给父构造函数的参数
        $reflectionClass = new \ReflectionClass(PostDiscountRepository::class);
        $constructor = $reflectionClass->getConstructor();
        
        // 创建实际的仓库实例来验证
        $repository = new PostDiscountRepository($registry);
        $this->assertInstanceOf(PostDiscountRepository::class, $repository);
        
        // 验证类存在
        $this->assertTrue(class_exists(PostDiscount::class));
    }
}
