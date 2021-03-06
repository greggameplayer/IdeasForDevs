<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\JobsAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobsAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobsAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobsAccount[]    findAll()
 * @method JobsAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobsAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobsAccount::class);
    }

    // /**
    //  * @return JobsAccount[] Returns an array of JobsAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function removeAllJobsAccountForAUser(Account $account) {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->execute();
    }

    /*
    public function findOneBySomeField($value): ?JobsAccount
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
