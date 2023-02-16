<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ArticleApiController extends AbstractApiController
{

    public function __construct(
        private ArticleRepository $articles
    ) {}

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
