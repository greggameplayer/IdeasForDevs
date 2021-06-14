<?php

namespace App\Repository;

use App\Entity\IsFor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IsFor|null find($id, $lockMode = null, $lockVersion = null)
 * @method IsFor|null findOneBy(array $criteria, array $orderBy = null)
 * @method IsFor[]    findAll()
 * @method IsFor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IsForRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IsFor::class);
    }

    // /**
    //  * @return IsFor[] Returns an array of IsFor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IsFor
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
