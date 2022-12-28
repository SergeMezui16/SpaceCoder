<?php

namespace App\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    public function __construct(private EntityService $entityService) {}
    
    #[Route('/', name: 'article')]
    public function list(PaginatorInterface $paginator, Request $request, ArticleRepository $articleRepository): Response
    {
        $pagination = $paginator->paginate(
            $articleRepository->findAllPublishedQuery($request->query->get('q', '')),
            $request->query->getInt('page', 1),
            12
        );
        
        return $this->render('article/index.html.twig', [
            'title' => 'Articles',
            'pagination' => $pagination
        ]);
    }

    #[Route('/{slug}', 'article_detail')]
    public function detail(
        Article $article, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        $this->entityService->incrementArticleViews($article);

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
            'article' => $article,
            'comments' => $article->getComments(),
            'form' => $form->createView()
        ]);
    }
}
