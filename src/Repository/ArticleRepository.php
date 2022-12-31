<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllPublishedQuery(string $q = ''): Query
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->select('a', 'c')
            ->join('a.comments', 'c')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;

        if($q !== ''){
            $queryBuilder
                ->andWhere('a.title LIKE :title')
                ->orWhere('a.subject LIKE :subject')
                ->orWhere('a.description LIKE :description')
                ->setParameter('title', "%$q%")
                ->setParameter('subject', "%$q%")
                ->setParameter('description', "%$q%")
                ->orderBy('a.title', 'ASC');
        } else{
            $queryBuilder->orderBy('a.publishedAt', 'DESC');
        }

        return $queryBuilder->getQuery();
    }

    public function findOneBySlug($slug): array
    {
        return
        $this
            ->createQueryBuilder('a') 
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function best(int $limit): array
   {
       return $this->createQueryBuilder('a')
            ->orderBy('a.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
       ;
   }

    public function views()
    {
        return $this->createQueryBuilder('a')
            ->select('SUM(a.views)')
            ->getQuery()
            ->getSingleResult()[1]
        ;
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
