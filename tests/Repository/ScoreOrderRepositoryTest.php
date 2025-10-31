<?php

namespace WechatPayScoreBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatPayBundle\Entity\Merchant;
use WechatPayBundle\Repository\MerchantRepository;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Enum\ScoreOrderState;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

/**
 * @internal
 */
#[CoversClass(ScoreOrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class ScoreOrderRepositoryTest extends AbstractRepositoryTestCase
{
    private ScoreOrderRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ScoreOrderRepository::class);
    }

    public function testSaveAndRemoveEntity(): void
    {
        $scoreOrder = $this->createTestScoreOrder();

        // Test save
        $this->repository->save($scoreOrder);
        $this->assertNotNull($scoreOrder->getId());

        // Test find saved entity
        $found = $this->repository->find($scoreOrder->getId());
        $this->assertInstanceOf(ScoreOrder::class, $found);

        // Store the ID before removing
        $entityId = $scoreOrder->getId();

        // Test remove
        $this->repository->remove($scoreOrder);
        $removedEntity = $this->repository->find($entityId);
        $this->assertNull($removedEntity);
    }

    public function testFindByMerchantAssociation(): void
    {
        $merchant = $this->createTestMerchant();
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setMerchant($merchant);

        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['merchant' => $merchant]);
        $this->assertCount(1, $results);
        $this->assertEquals($scoreOrder->getOutTradeNo(), $results[0]->getOutTradeNo());
    }

    public function testCountByMerchantAssociation(): void
    {
        $merchant = $this->createTestMerchant();
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setMerchant($merchant);

        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['merchant' => $merchant]);
        $this->assertEquals(1, $count);
    }

    public function testFindByStateAssociation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setState(ScoreOrderState::CREATED);

        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['state' => ScoreOrderState::CREATED]);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindBySpecificNotifyUrl(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $testUrl = 'https://test.example.com/callback';
        $scoreOrder->setNotifyUrl($testUrl);

        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['notifyUrl' => $testUrl]);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountBySpecificNotifyUrl(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $testUrl = 'https://test.example.com/callback';
        $scoreOrder->setNotifyUrl($testUrl);

        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['notifyUrl' => $testUrl]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByNullEndTime(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndTime(null);

        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['endTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));
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
        $scoreOrder->setState(ScoreOrderState::CREATED);

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

    public function testFindByNullEndTimeRemark(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndTimeRemark(null);
        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['endTimeRemark' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountByNullEndTime(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndTime(null);
        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['endTime' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByNullEndTimeRemark(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndTimeRemark(null);
        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['endTimeRemark' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testRemoveEntity(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $this->repository->save($scoreOrder);

        $entityId = $scoreOrder->getId();
        $this->assertNotNull($entityId);

        $this->repository->remove($scoreOrder);
        $removedEntity = $this->repository->find($entityId);
        $this->assertNull($removedEntity);
    }

    public function testFindByNullStartLocation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setStartLocation(null);
        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['startLocation' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByNullEndLocation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndLocation(null);
        $this->repository->save($scoreOrder);

        $results = $this->repository->findBy(['endLocation' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountByNullStartLocation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setStartLocation(null);
        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['startLocation' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByNullEndLocation(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setEndLocation(null);
        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['endLocation' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $scoreOrder = $this->createTestScoreOrder();
        $testService = '测试排序服务';
        $scoreOrder->setServiceIntroduction($testService);
        $this->repository->save($scoreOrder);

        $result = $this->repository->findOneBy(['serviceIntroduction' => $testService], ['serviceIntroduction' => 'ASC']);
        $this->assertInstanceOf(ScoreOrder::class, $result);
        $this->assertEquals($testService, $result->getServiceIntroduction());
    }

    public function testFindOneByAssociationMerchantShouldReturnMatchingEntity(): void
    {
        $merchant = $this->createTestMerchant();
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setMerchant($merchant);
        $this->repository->save($scoreOrder);

        $result = $this->repository->findOneBy(['merchant' => $merchant]);
        $this->assertInstanceOf(ScoreOrder::class, $result);
        $this->assertEquals($merchant->getId(), $result->getMerchant()?->getId());
    }

    public function testCountByAssociationMerchantShouldReturnCorrectNumber(): void
    {
        $merchant = $this->createTestMerchant();
        $scoreOrder = $this->createTestScoreOrder();
        $scoreOrder->setMerchant($merchant);
        $this->repository->save($scoreOrder);

        $count = $this->repository->count(['merchant' => $merchant]);
        $this->assertEquals(1, $count);
    }

    protected function createNewEntity(): object
    {
        return $this->createTestScoreOrder();
    }

    /**
     * @return ServiceEntityRepository<ScoreOrder>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
