<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleApiController extends AbstractApiController
{

    public function __construct(
        private ArticleRepository $articles
    ) {}

    /**
     * @OA\Get(
     *     path="/article/{slug}",
     *     summary="Info for a specific article",
     *     operationId="article",
     *     tags={"Article"},
     *     @OA\Parameter(ref="#/components/parameters/slug"),
     *     @OA\Parameter(
     *         name="comments",
     *         in="query",
     *         description="Return article detail with it's comments",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="An article details",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    #[Route('/article/{slug}', name: 'api_get_article', methods: ['GET'])]
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


    /**
     * @OA\Get(
     *     path="/articles",
     *     summary="List all articles",
     *     description="Une Bonne description bien longue",
     *     deprecated=false,
     *     operationId="articles",
     *     tags={"Article"},
     *     @OA\Response(
     *         response="200",
     *         description="List of Articles",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Articles") 
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
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
