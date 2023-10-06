<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
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

    public function add(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllQuery(string $q = ''): Query
    {
        return
            $this
            ->createQueryBuilder('p')
            ->andWhere('p.name LIKE :name')
            ->orWhere('p.description LIKE :description')
            ->setParameter('name', "%$q%")
            ->setParameter('description', "%$q%")
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * Find one project for API
     *
     * @param string $slug slug of Project
     * @return Project|null
     */
    public function findOneForApi(string $slug): ?Project
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p', 'r')
            ->leftJoin('p.role', 'r')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find All Projects for API
     *
     * @return Project[]
     */
    public function findAllForApi(): array
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p', 'r')
            ->leftJoin('p.role', 'r')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
