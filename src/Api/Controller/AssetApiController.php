<?php
namespace App\Api\Controller;


use App\Api\Controller\AbstractApiController;
use OpenApi\Attributes as OAT;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;

class AssetApiController extends AbstractApiController
{
    
    public function __construct(
        private Packages $packages,
        protected UrlHelper $urlHelper
    )
    {}


    #[OAT\Get(
        path: '/css/app',
        summary: 'Get css link',
        operationId: 'css',
        tags: ['Asset'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'CSS link',
                content: new OAT\JsonContent(type: 'string')
            )
        ]
    )]
    #[Route('/css/app', name: 'api_get_css', methods: ['GET'])]
    public function css(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.css')),
            200, 
            []
        );
    }

    #[OAT\Get(
        path: '/js/app',
        summary: 'Get js link',
        operationId: 'js',
        tags: ['Asset'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'JS link',
                content: new OAT\JsonContent(type: 'string')
            )
        ]
    )]
    #[Route('/js/app', name:'api_get_js', methods: ['GET'])]
    public function js(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.js')),
            200,
            []
        );
    }

    /**
     * Api Swagger UI
     *
     * @return Response
     */
    #[Route('/', name: 'api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', []);
    }
}
