<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\RessourceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class RessourceApiController extends AbstractApiController
{
    public function __construct(
        private RessourceRepository $ressources
    ) {}

    #[Route('/ressources', name: 'api_get_ressources', methods: ['GET'])]
    public function ressources(): JsonResponse
    {
        $ressources = $this->ressources->findAll();

        if ($ressources === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Ressources not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $ressources,
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }

    #[Route('/ressource/{slug}', name: 'api_get_ressource', methods: ['GET'])]
    public function ressource(Request $request): JsonResponse
    {
        $ressource = $this->ressources->findOneBy(['slug' => $request->get('slug')]);

        if ($ressource === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Ressource not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $ressource,
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }
}
