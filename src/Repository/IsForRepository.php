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
    public function findOneByLike($idUser, $idProject)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT is_for.id
               FROM is_for
               WHERE is_for.id_account_id = :idAccount
               AND is_for.evaluation = 1
               AND is_for.id_project_id = :idProject";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(["idProject" => $idProject, "idAccount" => $idUser])->fetchOne();
    }

    public function findOneByDislike($idUser, $idProject)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT is_for.id
               FROM is_for
               WHERE is_for.id_account_id = :idAccount
               AND is_for.evaluation = 0
               AND is_for.id_project_id = :idProject";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(["idProject" => $idProject, "idAccount" => $idUser])->fetchOne();
    }

    public function countVotesFor($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT COUNT(is_for.evaluation)
        FROM is_for
        WHERE is_for.id_project_id = :id
        AND is_for.evaluation = 1
        ";
        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }

    public function countVotesAgainst($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT COUNT(is_for.evaluation)
        FROM is_for
        WHERE is_for.id_project_id = :id
        AND is_for.evaluation = 0
        ";
        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }
}
