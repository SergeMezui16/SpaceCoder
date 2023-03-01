<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use OpenApi\Attributes as OAT;
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

    #[OAT\Get(
        path: '/comments/{id}',
        summary: 'Info for a specific comment',
        operationId: 'comment',
        tags: ['Comment'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'The id of the comment',
                schema: new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name: 'replies',
                in: 'query',
                description: 'Return comment detail with it\'s replies',
                required: false,
                schema: new OAT\Schema(type: 'boolean')                
            )
        ],
        responses: [ 
            new OAT\Response(
                response: 200,
                description: 'A comment details',
                content: [
                    new OAT\JsonContent(ref: '#/components/schemas/Comment')
                ]
            ),
            new OAT\Response(
                response: 201,
                description: 'A comment details + Replies',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(
                            property: 'comment',
                            title: 'Comment',
                            description: 'Comment details',
                            ref: '#/components/schemas/Comment'
                        ),
                        new OAT\Property(
                            property: 'replies',
                            title: 'Replies',
                            description: 'List of Replies of comment',
                            type: 'array',
                            items: new OAT\Items(ref: '#/components/schemas/Comment')
                        )
                    ]
                )
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]    
    )]
    #[Route('/comments/{id}', name: 'api_get_comment', methods: ['GET'])]
    public function comment(Request $request): JsonResponse
    {
        $id = (int) $request->get('id');
        $data = $this->comments->findOneForApi($id);

        if ($data === null || $id === 0) return $this->json(
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

    #[OAT\Get(
        path: '/replies/{id}',
        summary: 'Get replies of a comment',
        operationId: 'replies',
        tags: ['Comment'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'The id of the comment',
                schema: new OAT\Schema(type: 'integer')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'A comment details',
                content: [
                    new OAT\JsonContent(ref: '#/components/schemas/Comment')
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]    
    #[Route('/replies/{id}', name: 'api_get_replies', methods: ['GET'])]
    public function replies(Request $request): JsonResponse
    {
        $id = (int) $request->get('id');
        $replies = $this->comments->findAllRepliesForApi($id);

        if ($replies === null || $id === 0) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Comment not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $replies,
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }

    #[OAT\Get(
        path: '/comments',
        summary: 'List all comments',
        operationId: 'comments',
        tags: ['Comment'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Articles',
                content: [
                    new OAT\JsonContent(
                        type: 'array', 
                        items: new OAT\Items(ref: '#/components/schemas/Comments')
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]    
    #[Route('/comments', name: 'api_get_comments', methods: ['GET'])]
    public function comments(): JsonResponse
    {
        $comments = $this->comments->findAllForApi();

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

    #[OAT\Get(
        path: '/article-comments/{slug}',
        summary: 'Get comments of an article',
        operationId: 'commentsOfArticle',
        tags: ['Comment'],
        parameters: [
            new OAT\Parameter(
                name: 'slug',
                in: 'path',
                required: true,
                description: 'The slug of the article',
                schema: new OAT\Schema(type: 'string')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Article\'s comments',
                content: [
                    new OAT\JsonContent(
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Comments')
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
    #[Route('/article-comments/{slug}', name: 'api_get_comments_of_article', methods: ['GET'])]
    public function commentsOfArticle(Request $request): JsonResponse
    {
        $comments = $this->comments->findAllByArticleForApi($request->get('slug'));

        if ($comments === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Article not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $comments,
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }    
}