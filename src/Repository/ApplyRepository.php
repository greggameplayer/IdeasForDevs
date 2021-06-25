<?php

namespace App\Repository;

use App\Entity\Apply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apply[]    findAll()
 * @method Apply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apply::class);
    }

    // /**
    //  * @return Apply[] Returns an array of Apply objects
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
    public function findOneBySomeField($value): ?Apply
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function countProjectAsAdmin($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT COUNT(apply.id_project_id)
        FROM apply INNER JOIN role_project ON apply.role_project_id = role_project.id
        WHERE role_project.name = 'Administrateur'
        AND apply.id_account_id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }

    public function IdOfAdmins($idProject, $idUserConnected) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT apply.id_account_id
        FROM apply INNER JOIN role_project ON apply.role_project_id = role_project.id
        INNER JOIN account ON apply.id_account_id = account.id
        WHERE apply.id_project_id = :idProject
        AND role_project.name = 'Administrateur'
        AND account.is_verified = 1
AND account.id != :idUserConnected
        ";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['idProject' => $idProject, 'idUserConnected' => $idUserConnected])->fetchFirstColumn();
    }

    public function countProjectParticipation($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT COUNT(apply.id_project_id)
        FROM apply
        WHERE apply.id_account_id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }

    public function countProjectSuccessfull($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT COUNT(apply.id_project_id)
        FROM apply INNER JOIN Project ON apply.id_project_id = project.id INNER JOIN status ON project.status = status.id
        WHERE apply.id_account_id = :id
        AND status.status = 'Abouti'";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchOne();
    }

}
