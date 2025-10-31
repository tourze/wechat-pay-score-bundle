<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatPayBundle\Entity\Merchant;
use WechatPayBundle\Repository\MerchantRepository;
use WechatPayScoreBundle\Entity\PostDiscount;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Repository\PostDiscountRepository;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

/**
 * @internal
 */
#[CoversClass(PostDiscountRepository::class)]
#[RunTestsInSeparateProcesses]
final class PostDiscountRepositoryTest extends AbstractRepositoryTestCase
{
    private PostDiscountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PostDiscountRepository::class);
    }

    public function testSaveAndFindPostDiscount(): void
    {
        $postDiscount = $this->createTestPostDiscount();

        // Test save
        $this->repository->save($postDiscount);
        $this->assertNotNull($postDiscount->getId());

        // Test find saved entity
        $found = $this->repository->find($postDiscount->getId());
        $this->assertInstanceOf(PostDiscount::class, $found);
        $this->assertEquals($postDiscount->getName(), $found->getName());
    }

    public function testRemovePostDiscount(): void
    {
        $postDiscount = $this->createTestPostDiscount();
        $this->repository->save($postDiscount);

        $id = $postDiscount->getId();
        $this->assertNotNull($id);

        // Test remove
        $this->repository->remove($postDiscount);
        $removedEntity = $this->repository->find($id);
        $this->assertNull($removedEntity);
    }

    public function testFindByName(): void
    {
        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setName('优惠券测试');

        $this->repository->save($postDiscount);

        $results = $this->repository->findBy(['name' => '优惠券测试']);
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertEquals('优惠券测试', $results[0]->getName());
    }

    public function testCountPostDiscounts(): void
    {
        $postDiscount = $this->createTestPostDiscount();
        $this->repository->save($postDiscount);

        $count = $this->repository->count([]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    private function createTestPostDiscount(): PostDiscount
    {
        $scoreOrder = $this->createTestScoreOrder();

        $postDiscount = new PostDiscount();
        $postDiscount->setName('测试优惠券');
        $postDiscount->setDescription('测试用优惠券描述');
        $postDiscount->setCount(1);
        $postDiscount->setScoreOrder($scoreOrder);

        return $postDiscount;
    }

    private function createTestScoreOrder(): ScoreOrder
    {
        $merchant = $this->createTestMerchant();

        $scoreOrder = new ScoreOrder();
        $scoreOrder->setMerchant($merchant);
        $scoreOrder->setOutTradeNo('TEST_' . uniqid());
        $scoreOrder->setAppId('test_app_id');
        $scoreOrder->setServiceId('test_service_id');
        $scoreOrder->setServiceIntroduction('测试服务');
        $scoreOrder->setStartTime('2024-01-01T12:00:00+08:00');
        $scoreOrder->setStartTimeRemark('开始时间备注');
        $scoreOrder->setRiskFundName('DEPOSIT');
        $scoreOrder->setRiskFundAmount(100);
        $scoreOrder->setRiskFundDescription('押金');
        $scoreOrder->setNotifyUrl('https://example.com/notify');

        self::getService(ScoreOrderRepository::class)->save($scoreOrder);

        return $scoreOrder;
    }

    private function createTestMerchant(): Merchant
    {
        $merchant = new Merchant();
        $merchant->setMchId('test_mch_id_' . uniqid());
        $merchant->setApiKey('test_api_key');
        $merchant->setPemKey('test_pem_key');
        $merchant->setCertSerial('test_cert_serial');
        $merchant->setPemCert('test_pem_cert');
        $merchant->setRemark('测试商户');
        $merchant->setValid(true);

        self::getService(MerchantRepository::class)->save($merchant);

        return $merchant;
    }

    public function testFindOneByShouldRespectOrderByClause(): void
    {
        // 清理现有数据
        $existingDiscounts = $this->repository->findAll();
        foreach ($existingDiscounts as $discount) {
            $this->repository->remove($discount);
        }

        $postDiscount1 = $this->createTestPostDiscount();
        $postDiscount1->setName('Z最后优惠');
        $this->repository->save($postDiscount1);

        $postDiscount2 = $this->createTestPostDiscount();
        $postDiscount2->setName('A最前优惠');
        $this->repository->save($postDiscount2);

        // 按名称升序排序，应该返回第一个
        $result = $this->repository->findOneBy([], ['name' => 'ASC']);
        $this->assertInstanceOf(PostDiscount::class, $result);
        $this->assertEquals('A最前优惠', $result->getName());

        // 按名称降序排序，应该返回最后一个
        $result = $this->repository->findOneBy([], ['name' => 'DESC']);
        $this->assertInstanceOf(PostDiscount::class, $result);
        $this->assertEquals('Z最后优惠', $result->getName());
    }

    public function testFindByScoreOrderAssociation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();

        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setScoreOrder($scoreOrder);
        $this->repository->save($postDiscount);

        $results = $this->repository->findBy(['scoreOrder' => $scoreOrder]);
        $this->assertNotEmpty($results);
        $this->assertEquals($scoreOrder->getId(), $results[0]->getScoreOrder()?->getId());
    }

    public function testCountByScoreOrderAssociation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();

        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setScoreOrder($scoreOrder);
        $this->repository->save($postDiscount);

        $count = $this->repository->count(['scoreOrder' => $scoreOrder]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByNullCount(): void
    {
        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setCount(null); // 设置可空字段为null
        $this->repository->save($postDiscount);

        $results = $this->repository->findBy(['count' => null]);
        $this->assertNotEmpty($results);
        $this->assertNull($results[0]->getCount());
    }

    public function testCountByNullCount(): void
    {
        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setCount(null);
        $this->repository->save($postDiscount);

        $count = $this->repository->count(['count' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByAssociationScoreOrderShouldReturnMatchingEntity(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setScoreOrder($scoreOrder);
        $this->repository->save($postDiscount);

        $result = $this->repository->findOneBy(['scoreOrder' => $scoreOrder]);
        $this->assertInstanceOf(PostDiscount::class, $result);
        $this->assertEquals($scoreOrder->getId(), $result->getScoreOrder()?->getId());
    }

    public function testCountByAssociationScoreOrderShouldReturnCorrectNumber(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $postDiscount = $this->createTestPostDiscount();
        $postDiscount->setScoreOrder($scoreOrder);
        $this->repository->save($postDiscount);

        $count = $this->repository->count(['scoreOrder' => $scoreOrder]);
        $this->assertEquals(1, $count);
    }

    protected function createNewEntity(): object
    {
        return $this->createTestPostDiscount();
    }

    /**
     * @return ServiceEntityRepository<PostDiscount>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
