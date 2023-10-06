<?php

namespace App\Repository;

use App\Servant\Entity\ServantLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServantLevel>
 *
 * @method ServantLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServantLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServantLevel[]    findAll()
 * @method ServantLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServantLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServantLevel::class);
    }

//    /**
//     * @return ServantLevel[] Returns an array of ServantLevel objects
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

//    public function findOneBySomeField($value): ?ServantLevel
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
