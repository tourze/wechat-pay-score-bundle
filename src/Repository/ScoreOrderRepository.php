<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * @extends ServiceEntityRepository<ScoreOrder>
 */
#[AsRepository(entityClass: ScoreOrder::class)]
class ScoreOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScoreOrder::class);
    }

    public function save(ScoreOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ScoreOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
