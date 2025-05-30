<?php

namespace WechatPayScoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatPayScoreBundle\Entity\PostPayment;

/**
 * @method PostPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostPayment[]    findAll()
 * @method PostPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostPaymentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostPayment::class);
    }
}
