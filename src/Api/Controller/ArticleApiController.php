<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleApiController extends AbstractApiController
{

    public function __construct(
        private ArticleRepository $articles
    ) {}

    #[OAT\Get(
        path: '/articles/{slug}',
        summary: 'Info for a specific article',
        operationId: 'article',
        tags: ['Article'],
        parameters: [
            new OAT\Parameter(ref: '#/components/parameters/slug'),
            new OAT\Parameter(
                name: 'comments',
                in: 'query',
                description: 'Return article detail with it\'s comments',
                required: false,
                schema: new OAT\Schema(type: 'boolean')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200, 
                description: 'An article details', 
                content: new OAT\JsonContent(ref: '#/components/schemas/Article')
            ),
            new OAT\Response(
                response: 404, 
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
    #[Route('/articles/{slug}', name: 'api_get_article', methods: ['GET'])]
    public function article(Request $request): JsonResponse
    {
        $data = $this->articles->findOneBySlugPublished($request->get('slug'));

        if ($data === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Article not found.'
            ],
            Response::HTTP_NOT_FOUND
        );


        if ($request->query->getBoolean('comments') === true) {
            $data = [
                'article' => $data,
                'comments' => $data->getComments()
            ];
        }

        return $this->json(
            $data,
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }


    #[OAT\Get(
        path: '/articles',
        summary: 'List all articles',
        description: 'Une Bonne description bien longue',
        deprecated: false,
        operationId: 'articles',
        tags: ['Article'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Articles',
                content: new OAT\JsonContent(
                    type: 'array',
                    items: new OAT\Items(ref: '#/components/schemas/Articles')                    
                )
            ),
            new OAT\Response(
                response: 404, 
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
    #[Route('/articles', name: 'api_get_articles', methods: ['GET'])]
    public function articles(): JsonResponse
    {
        $articles = $this->articles->findAllPublishedQuery()->getResult();

        if ($articles === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Articles not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $articles,
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }
}
