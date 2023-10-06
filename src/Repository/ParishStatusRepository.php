<?php

namespace App\Repository;

use App\Servant\Entity\ParishStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParishStatus>
 *
 * @method ParishStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParishStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParishStatus[]    findAll()
 * @method ParishStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParishStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParishStatus::class);
    }

//    /**
//     * @return ParishStatus[] Returns an array of ParishStatus objects
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

//    public function findOneBySomeField($value): ?ParishStatus
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
