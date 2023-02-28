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

    public function findAllByArticle(int $id)
    {
        return 
            $this
                ->createQueryBuilder('c')
                ->select('c', 'r', 'rt')
                ->join('c.replies', 'r')
                ->join('c.replyTo', 'rt')
                ->andWhere('c.article = :val')
                ->setParameter('val', $id)
                ->orderBy('c.updateAt')
                ->getQuery()
                ->getResult()
        ;
    }

    public function last(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findOneForApi(int $id)
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
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findOneByArticleForApi(string $slug)
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->where('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllForApi()
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllRepliesForApi(int $id)
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c', 'a', 'r', 'au', 'rt')
            ->leftJoin('c.author', 'au')
            ->leftJoin('c.article', 'a')
            ->leftJoin('c.replies', 'r')
            ->leftJoin('c.replyTo', 'rt')
            ->where('rt.id = :id')
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
