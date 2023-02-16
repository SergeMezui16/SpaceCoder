<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class CommentApiController extends AbstractApiController
{

    public function __construct(
        private CommentRepository $comments,
        private ArticleRepository $articles
    ) {
    }

    
    #[Route('/comment/{id}', name: 'api_get_comment', methods: ['GET'])]
    public function comment(Request $request): JsonResponse
    {
        $data = $this->comments->findOneBy(['id' => $request->get('id')]);

        if ($data === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Comment not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        if ($request->query->getBoolean('replies') === true){
            $data = [
                'comment' => $data,
                'replies' => $data->getReplies()
            ];
        }

        return $this->json(
            $data,
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }

    #[Route('/replies/{id}', name: 'api_get_replies', methods: ['GET'])]
    public function replies(Request $request): JsonResponse
    {
        $comment = $this->comments->findOneBy(['id' => $request->get('id')]);

        if ($comment === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Comment not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $comment->getReplies(),
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }

    #[Route('/comments', name: 'api_get_comments', methods: ['GET'])]
    public function comments(): JsonResponse
    {
        $comments = $this->comments->findAll();

        if ($comments === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Comments not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $comments,
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }

    #[Route('/comments/{slug}', name: 'api_get_comments_of_article', methods: ['GET'])]
    public function commentsOfArticle(Request $request): JsonResponse
    {
        $article = $this->articles->findOneBy(['slug' => $request->get('slug')]);

        if ($article === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Article not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $article->getComments(),
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }


    
}
