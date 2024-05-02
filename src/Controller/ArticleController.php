<?php

namespace App\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Event\CommentCreatedEvent;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\EarnCoinService;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article', name: 'article.')]
class ArticleController extends AbstractController
{
    public function __construct(private readonly EntityService $entityService)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request, ArticleRepository $articleRepository): Response
    {
        $q = $request->query->get('q', '');

        $query = $this->isGranted('ROLE_ADMIN')
            ? $articleRepository->findAllQuery($q)
            : $articleRepository->findAllPublishedQuery($q);

        return $this->render('pages/articles/index.html.twig', [
            'title' => 'Articles',
            'pagination' => $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                12
            )
        ]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(
        Article                  $article,
        Request                  $request,
        EntityManagerInterface   $entityManager,
        EarnCoinService          $earner,
        EventDispatcherInterface $dispatcher
    ): Response
    {
        if (
            $article->getPublishedAt() >= new \DateTimeImmutable() &&
            !$this->isGranted('ROLE_ADMIN')
        ) throw new NotFoundHttpException();

        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        if ($auth) $earner->firstViewer($article, $auth->getUser());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if (!$this->isGranted('ROLE_ADMIN')) $this->entityService->incrementArticleViews($article);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->isGranted('IS_AUTHENTICATED');

            $comment
                ->setAuthor($auth->getUser())
                ->setArticle($article);

            $entityManager->persist($comment);
            $entityManager->flush();

            $dispatcher->dispatch(new CommentCreatedEvent($comment, $auth));

            return $this->redirectToRoute('article.index', [
                'slug' => $article->getSlug(),
                '_fragment' => 'comment-' . $comment->getId()
            ]);
        }

        return $this->render('pages/articles/show.html.twig', [
            'article' => $article,
            'comments' => $article->getComments(),
            'form' => $form
        ]);
    }
}
