<?php

namespace App\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\EarnCoinService;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        EntityManagerInterface $entityManager,
        EarnCoinService $earner
    ): Response
    {
        if($article->getPublishedAt() > new \DateTimeImmutable()){
            throw new NotFoundHttpException();
        }

        /** @var UserAuthentication */
        $auth = $this->getUser();

        if($auth) $earner->firstViewer($article, $auth->getUser());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if(!$this->isGranted('ROLE_ADMIN')) $this->entityService->incrementArticleViews($article);

        if($form->isSubmitted() && $form->isValid()){

            $this->isGranted('IS_AUTHENTICATED');

            $comment
                ->setAuthor($auth->getUser())
                ->setArticle($article)
            ;

            $earner->commentOn($auth->getUser());
            $earner->firstComment($auth->getUser());
            $earner->firstCommentOn($article, $auth->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();
            
            return $this->redirectToRoute('article_detail', [
                'slug' => $article->getSlug(),
                '_fragment' => 'comment-' . $comment->getId()
            ]);
        }

        return $this->render('article/detail.html.twig', [
            'article' => $article,
            'comments' => $article->getComments(),
            'form' => $form->createView()
        ]);
    }
}
