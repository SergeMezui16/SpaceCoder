<?php

namespace App\Repository;

use App\Servant\Entity\Parish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parish>
 *
 * @method Parish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parish[]    findAll()
 * @method Parish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parish::class);
    }

//    /**
//     * @return Parish[] Returns an array of Parish objects
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

//    public function findOneBySomeField($value): ?Parish
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
