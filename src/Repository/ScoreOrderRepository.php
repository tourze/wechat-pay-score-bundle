<?php

namespace WechatPayScoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatPayScoreBundle\Entity\ScoreOrder;

/**
 * @method ScoreOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreOrder[]    findAll()
 * @method ScoreOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreOrderRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScoreOrder::class);
    }
}
