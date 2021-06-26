<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByName($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :val')
            ->setParameter('val', "%$value%")
            ->getQuery()
            ->getResult()
        ;
    }

    public function detailsProject($id) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT project.id, project.name, project.repo, project.description, project.date_creation, project.id_mongo, status.status
        FROM project INNER JOIN status ON project.status = status.id
        WHERE project.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAllAssociative()[0];
    }

    public function skillsAndJobsNeeded($id) :array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT project.skills_needed, project.job_needed
        FROM project
        WHERE project.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAllAssociative()[0];
    }


}
