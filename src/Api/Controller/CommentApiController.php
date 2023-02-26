<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentApiController extends AbstractApiController
{

    public function __construct(
        private CommentRepository $comments,
        private ArticleRepository $articles
    ) {
    }

    /**
     * @OA\Get(
     *     path="/comment/{id}",
     *     summary="Info for a specific comment",
     *     operationId="comment",
     *     tags={"Comment"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The id of the comment",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="replies",
     *         in="query",
     *         description="Return comment detail with it's replies",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="A comment details",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/replies/{id}",
     *     summary="Get replies of a comment",
     *     operationId="replies",
     *     tags={"Comment"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The id of the comment",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="A comment details",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/comments",
     *     summary="List all comments",
     *     operationId="comments",
     *     tags={"Comment"},
     *     @OA\Response(
     *         response="200",
     *         description="List of Articles",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Comments") 
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/comments/{slug}",
     *     summary="Get comments of an article",
     *     operationId="commentsOfArticle",
     *     tags={"Comment"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="The slug of the article",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of Article's comments",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Comments") 
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
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
