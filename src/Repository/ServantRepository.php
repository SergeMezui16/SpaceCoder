<?php

namespace App\Repository;

use App\Servant\Entity\Servant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Servant>
 *
 * @method Servant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Servant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Servant[]    findAll()
 * @method Servant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Servant::class);
    }

//    /**
//     * @return Servant[] Returns an array of Servant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Servant
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
