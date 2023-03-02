<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\RessourceRepository;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RessourceApiController extends AbstractApiController
{
    public function __construct(
        private RessourceRepository $ressources
    ) {}

    #[OAT\Get(
        path: '/ressources',
        summary: 'List all ressources',
        operationId: 'ressources',
        tags: ['Ressource'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Ressources',
                content: [
                    new OAT\JsonContent(
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Ressource')
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
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

    #[OAT\Get(
        path: '/ressources/{slug}',
        summary: 'Info for a specific ressource',
        operationId: 'ressource',
        tags: ['Ressource'],
        parameters: [new OAT\Parameter(ref: '#/components/parameters/slug')],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'A Ressource details',
                content: [
                    new OAT\JsonContent(
                        ref: '#/components/schemas/Ressource'
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
    #[Route('/ressources/{slug}', name: 'api_get_ressource', methods: ['GET'])]
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
