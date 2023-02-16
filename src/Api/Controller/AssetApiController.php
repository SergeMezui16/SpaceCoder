<?php
namespace App\Api\Controller;


use App\Api\Controller\AbstractApiController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    
    #[Route('/css/app', name: 'api_get_css', methods: ['GET'])]
    public function css(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.css')),
            200, 
            []
        );
    }

    #[Route('/js/app', name:'api_get_js', methods: ['GET'])]
    public function js(): JsonResponse
    {
        return $this->json(
            $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/app.js')),
            200,
            []
        );
    }
}
