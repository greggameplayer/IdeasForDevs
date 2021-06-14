<?php

namespace App\Repository;

use App\Entity\ReportProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReportProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportProject[]    findAll()
 * @method ReportProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportProject::class);
    }

    // /**
    //  * @return ReportProject[] Returns an array of ReportProject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReportProject
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
