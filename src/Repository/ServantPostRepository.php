<?php

namespace App\Repository;

use App\Servant\Entity\ServantPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServantPost>
 *
 * @method ServantPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServantPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServantPost[]    findAll()
 * @method ServantPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServantPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServantPost::class);
    }

//    /**
//     * @return ServantPost[] Returns an array of ServantPost objects
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

//    public function findOneBySomeField($value): ?ServantPost
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
