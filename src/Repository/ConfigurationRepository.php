<?php

namespace App\Repository;

use App\Entity\Configuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Configuration>
 *
 * @method Configuration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Configuration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Configuration[]    findAll()
 * @method Configuration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    public function add(Configuration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Configuration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    } 


    /**
    * Find a configuration by name
    *
    * @param string $name
    * @return Configuration|null
    */
    public function findOneByConstNameField(string $name) : ?Configuration
    {
        return 
            $this
                ->createQueryBuilder('c')
                ->andWhere('c.name = :val')
                ->setParameter('val', $name)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }

    /**
     * Find a Collection of configuration by category name
     *
     * @param string $category
     * @return Configuration[]|null
     */
    public function findByCategory(string $category) : ?array
    {

        return 
            $this
                ->createQueryBuilder('c')
                ->andWhere('c.category = :val')
                ->setParameter('val', $category)
                ->getQuery()
                ->getResult()
        ;
    }

//    /**
//     * @return Configuration[] Returns an array of Configuration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Configuration
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
