<?php

namespace App\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article')]
    public function list(PaginatorInterface $paginator, Request $request, ArticleRepository $articleRepository): Response
    {
        $pagination = $paginator->paginate(
            $articleRepository->findAllPublishedQuery($request->query->get('q', '')),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('article/index.html.twig', [
            'title' => 'Articles',
            'pagination' => $pagination
        ]);
    }

    #[Route('/{slug}', 'article_detail')]
    public function detail(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->isGranted('IS_AUTHENTICATED');

            /** @var UserAuthentication */
            $auth = $this->getUser();

            $comment
                ->setAuthor($auth->getUser())
                ->setArticle($article)
            ;

            $entityManager->persist($comment);
            $entityManager->flush();

            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
        }

        return $this->render('article/detail.html.twig', [
            'title' => $article->getSlug(),
            'article' => $article,
            'comments' => $article->getComments(),
            'form' => $form->createView()
        ]);
    }
}
