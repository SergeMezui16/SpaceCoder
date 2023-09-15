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

    /**
     * Find one Article published by slug for API
     *
     * @param string $slug
     * @return Article|null
     */
    public function findOneForApi(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'c', 'au', 's', 'r', 'rt', 'cau')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('a.author', 'au')
            ->leftJoin('c.author', 'cau')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->leftJoin('a.suggestedBy', 's')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)

            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find All published Articles for API
     *
     * @return Article[]
     */
    public function findAllForApi(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'c')
            ->leftJoin('a.comments', 'c')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find All Published for Search Engine
     *
     * @param string $q query
     * @return Query
     */
    public function findAllPublishedQuery(string $q = ''): Query
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->select('a', 'c')
            ->leftJoin('a.comments', 'c')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;

        if($q !== ''){
            $queryBuilder
                ->andWhere('a.title LIKE :q')
                ->orWhere('a.subject LIKE :q')
                ->orWhere('a.description LIKE :q')
                ->setParameter('q', "%$q%")
                ->orderBy('a.title', 'ASC');
        } else{
            $queryBuilder->orderBy('a.publishedAt', 'DESC');
        }

        return $queryBuilder->getQuery();
    }

    /**
     * Find All for Admin to Search Engine
     *
     * @param string $q
     * @return Query
     */
    public function findAllQuery(string $q = ''): Query
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->select('a', 'c')
            ->leftJoin('a.comments', 'c');

        if ($q !== '') {
            $queryBuilder
                ->andWhere('a.title LIKE :q')
                ->orWhere('a.subject LIKE :q')
                ->orWhere('a.description LIKE :q')
                ->setParameter('q', "%$q%")
                ->orderBy('a.title', 'ASC')
            ;
        } else {
            $queryBuilder->orderBy('a.publishedAt', 'DESC');
        }

        return $queryBuilder->getQuery();
    }

    /**
     * Get the bests Articles order by views
     *
     * @param integer $limit
     * @return array
     */
    public function best(int $limit): array
   {
       return $this->createQueryBuilder('a')
            ->orderBy('a.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
       ;
   }

   /**
    * Get the sum of all views
    *
    * @return integer
    */
    public function views(): int
    {
        return $this->createQueryBuilder('a')
            ->select('SUM(a.views)')
            ->getQuery()
            ->getSingleScalarResult()
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
