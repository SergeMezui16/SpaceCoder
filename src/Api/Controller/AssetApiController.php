<?php
namespace App\Api\Controller;


use App\Api\Controller\AbstractApiController;
use OpenApi\Annotations as OA;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AssetApiController extends AbstractApiController
{
    
    public function __construct(
        private Packages $packages,
        protected UrlHelper $urlHelper
    )
    {}

    /**
     * @OA\Get(
     *     path="/css/app",
     *     summary="Get css link",
     *     operationId="css",
     *     tags={"Asset"},
     *     @OA\Response(
     *         response="200",
     *         description="CSS link",
     *         @OA\JsonContent(type="string")
     *     )
     * )
     */
    #[Route('/css/app', name: 'api_get_css', methods: ['GET'])]
    public function css(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.css')),
            200, 
            []
        );
    }

    /**
     * @OA\Get(
     *     path="/js/app",
     *     summary="Get js link",
     *     operationId="js",
     *     tags={"Asset"},
     *     @OA\Response(
     *         response="200",
     *         description="JS link",
     *         @OA\JsonContent(type="string")
     *     )
     * )
     */
    #[Route('/js/app', name:'api_get_js', methods: ['GET'])]
    public function js(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.js')),
            200,
            []
        );
    }



    #[Route('/', name: 'api')]
    public function index(): Response
    {
        return $this->render('index/doc.html.twig', []);
    }
}
