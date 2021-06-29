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
            ->getResult();
    }


    public function findOneByNameAndId($search, $idProject)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT project.id
        FROM project
        WHERE project.id = :idProject AND project.name LIKE :search";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['idProject' => $idProject, 'search' => "%$search%"])->fetchOne();
    }

    public function detailsProject($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT project.id, project.name, project.repo, project.description, project.date_creation, project.id_mongo, status.status
        FROM project INNER JOIN status ON project.status_id = status.id
        WHERE project.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAllAssociative()[0];
    }

    public function skillsAndJobsNeeded($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT project.skills_needed, project.job_needed
        FROM project
        WHERE project.id = :id";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['id' => $id])->fetchAllAssociative()[0];
    }

    public function findAllProjectsOrderByLike($search = "")
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        (SELECT project.*, account.firstname as firstname, account.lastname as lastname, SUM(is_for.evaluation) as likes, (COUNT(is_for.evaluation) - SUM(is_for.evaluation)) as dislike, account.id as accountId
        FROM project
        INNER JOIN account ON project.account_id = account.id
        INNER JOIN is_for ON is_for.id_project_id = project.id
        WHERE project.name LIKE :search
        GROUP BY project.id ORDER BY SUM(is_for.evaluation) - (COUNT(is_for.evaluation) - SUM(is_for.evaluation)) DESC)
        UNION
        (SELECT project.*, account.firstname as firstname, account.lastname as lastname, 0 as likes, 0 as dislike, account.id as accountId
        FROM project, account, is_for
        WHERE project.id NOT IN (
        SELECT is_for.id_project_id 
        FROM is_for)
        AND project.account_id = account.id
        AND project.name LIKE :search
        GROUP BY project.id)
        ";

        $stmt = $conn->prepare($sql);

        return $stmt->executeQuery(['search' => '%' . $search . '%'])->fetchAllAssociative();
    }


}
