<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatPayBundle\Entity\Merchant;
use WechatPayBundle\Repository\MerchantRepository;
use WechatPayScoreBundle\Entity\PostPayment;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Repository\PostPaymentRepository;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

/**
 * @internal
 */
#[CoversClass(PostPaymentRepository::class)]
#[RunTestsInSeparateProcesses]
final class PostPaymentRepositoryTest extends AbstractRepositoryTestCase
{
    private PostPaymentRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PostPaymentRepository::class);
    }

    public function testSaveAndFindPostPayment(): void
    {
        $postPayment = $this->createTestPostPayment();

        // Test save
        $this->repository->save($postPayment);
        $this->assertNotNull($postPayment->getId());

        // Test find saved entity
        $found = $this->repository->find($postPayment->getId());
        $this->assertInstanceOf(PostPayment::class, $found);
        $this->assertEquals($postPayment->getName(), $found->getName());
    }

    public function testRemovePostPayment(): void
    {
        $postPayment = $this->createTestPostPayment();
        $this->repository->save($postPayment);

        $id = $postPayment->getId();
        $this->assertNotNull($id);

        // Test remove
        $this->repository->remove($postPayment);
        $removedEntity = $this->repository->find($id);
        $this->assertNull($removedEntity);
    }

    public function testFindByName(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setName('后付费测试');

        $this->repository->save($postPayment);

        $results = $this->repository->findBy(['name' => '后付费测试']);
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertEquals('后付费测试', $results[0]->getName());
    }

    public function testCountPostPayments(): void
    {
        $postPayment = $this->createTestPostPayment();
        $this->repository->save($postPayment);

        $count = $this->repository->count([]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    private function createTestPostPayment(): PostPayment
    {
        $scoreOrder = $this->createTestScoreOrder();

        $postPayment = new PostPayment();
        $postPayment->setName('测试后付费项目');
        $postPayment->setDescription('测试用后付费项目描述');
        $postPayment->setAmount(1000);
        $postPayment->setCount(1);
        $postPayment->setScoreOrder($scoreOrder);

        return $postPayment;
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

    public function testFindByScoreOrderAssociation(): void
    {
        $postPayment = $this->createTestPostPayment();
        $this->repository->save($postPayment);

        $scoreOrder = $postPayment->getScoreOrder();
        $this->assertNotNull($scoreOrder);

        $results = $this->repository->findBy(['scoreOrder' => $scoreOrder]);
        $this->assertNotEmpty($results);
        $this->assertEquals($postPayment->getId(), $results[0]->getId());
    }

    public function testCountByScoreOrderAssociation(): void
    {
        $postPayment = $this->createTestPostPayment();
        $this->repository->save($postPayment);

        $scoreOrder = $postPayment->getScoreOrder();
        $this->assertNotNull($scoreOrder);

        $count = $this->repository->count(['scoreOrder' => $scoreOrder]);
        $this->assertEquals(1, $count);
    }

    public function testFindByNullName(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setName(null);
        $this->repository->save($postPayment);

        $results = $this->repository->findBy(['name' => null]);
        $this->assertNotEmpty($results);
        $this->assertNull($results[0]->getName());
    }

    public function testFindByNullDescription(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setDescription(null);
        $this->repository->save($postPayment);

        $results = $this->repository->findBy(['description' => null]);
        $this->assertNotEmpty($results);
        $this->assertNull($results[0]->getDescription());
    }

    public function testFindByNullCount(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setCount(null);
        $this->repository->save($postPayment);

        $results = $this->repository->findBy(['count' => null]);
        $this->assertNotEmpty($results);
        $this->assertNull($results[0]->getCount());
    }

    public function testCountWithNullName(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setName(null);
        $this->repository->save($postPayment);

        $count = $this->repository->count(['name' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithNullDescription(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setDescription(null);
        $this->repository->save($postPayment);

        $count = $this->repository->count(['description' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithNullCount(): void
    {
        $postPayment = $this->createTestPostPayment();
        $postPayment->setCount(null);
        $this->repository->save($postPayment);

        $count = $this->repository->count(['count' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBy(): void
    {
        // 清理现有数据
        $existingPayments = $this->repository->findAll();
        foreach ($existingPayments as $payment) {
            $this->repository->remove($payment);
        }

        $postPayment1 = $this->createTestPostPayment();
        $postPayment1->setName('Z后付费');
        $this->repository->save($postPayment1);

        $postPayment2 = $this->createTestPostPayment();
        $postPayment2->setName('A后付费');
        $this->repository->save($postPayment2);

        $result = $this->repository->findOneBy([], ['name' => 'ASC']);
        $this->assertInstanceOf(PostPayment::class, $result);
        $this->assertEquals('A后付费', $result->getName());
    }

    public function testFindOneByAssociationScoreOrderShouldReturnMatchingEntity(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $postPayment = $this->createTestPostPayment();
        $postPayment->setScoreOrder($scoreOrder);
        $this->repository->save($postPayment);

        $result = $this->repository->findOneBy(['scoreOrder' => $scoreOrder]);
        $this->assertInstanceOf(PostPayment::class, $result);
        $this->assertEquals($scoreOrder->getId(), $result->getScoreOrder()?->getId());
    }

    public function testCountByAssociationScoreOrderShouldReturnCorrectNumber(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $postPayment = $this->createTestPostPayment();
        $postPayment->setScoreOrder($scoreOrder);
        $this->repository->save($postPayment);

        $count = $this->repository->count(['scoreOrder' => $scoreOrder]);
        $this->assertEquals(1, $count);
    }

    protected function createNewEntity(): object
    {
        return $this->createTestPostPayment();
    }

    /**
     * @return ServiceEntityRepository<PostPayment>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
