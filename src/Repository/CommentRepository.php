<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find All comment by article id
     *
     * @param integer $id id of article
     * @return Comment[]
     */
    public function findAllByArticle(int $id): array
    {
        return 
            $this
                ->createQueryBuilder('c')
                ->select('c', 'r', 'rt')
                ->join('c.replies', 'r')
                ->join('c.replyTo', 'rt')
                ->andWhere('c.article = :val')
                ->setParameter('val', $id)
                ->orderBy('c.updatedAt')
                ->getQuery()
                ->getResult()
        ;
    }

    /**
     * Find lasts comments published
     *
     * @param integer $limit
     * @return Comment[]
     */
    public function last(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find one Comment for API
     *
     * @param integer $id id of comment
     * @return Comment|null
     */
    public function findOneForApi(int $id): ?Comment
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt', 'rau', 'ra', 'rrt', 'rr')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('r.author', 'rau')
            ->leftJoin('r.article', 'ra')
            ->leftJoin('r.replyTo', 'rrt')
            ->leftJoin('r.replies', 'rr')
            ->leftJoin('c.replyTo', 'rt')
            ->where('c.id = :id')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find All comments of an artcle for API
     *
     * @param string $slug article slug
     * @return Comment[]
     */
    public function findAllByArticleForApi(string $slug): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->where('a.slug = :slug')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find All comment for API
     *
     * @return Comment[]
     */
    public function findAllForApi(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->where('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find All replies of a comment for API
     *
     * @param integer $id
     * @return Comment[]
     */
    public function findAllRepliesForApi(int $id): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->where('rt.id = :id')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
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

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
