<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    // /**
    //  * @return Account[] Returns an array of Account objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function detailsAdminProjectForEachAdmin($id) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT account.id, account.firstname, account.lastname, account.subscribe_date, job.name
        FROM account INNER JOIN jobs_account ON account.id = jobs_account.account_id 
            INNER JOIN job ON jobs_account.job_id = job.id
        WHERE account.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAllAssociative()[0];
    }

    public function skillsForEachAdmin($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT account.skills
        FROM account
        WHERE account.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }

}
