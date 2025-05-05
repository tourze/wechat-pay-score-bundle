<?php

namespace WechatPayScoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatPayScoreBundle\Entity\PostDiscount;

/**
 * @method PostDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostDiscount[]    findAll()
 * @method PostDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostDiscountRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostDiscount::class);
    }
}
