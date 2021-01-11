<?php

namespace App\Repository;

use App\Entity\CheeseListingNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CheeseListingNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheeseListingNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheeseListingNotification[]    findAll()
 * @method CheeseListingNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheeseListingNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheeseListingNotification::class);
    }

    // /**
    //  * @return CheeseListingNotification[] Returns an array of CheeseListingNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CheeseListingNotification
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
