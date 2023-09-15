<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ProjectRepository;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectApiController extends AbstractApiController
{
    public function __construct(
        private ProjectRepository $projects
    ) {}

    
    #[OAT\Get(
        path: '/projects',
        summary: 'List all projects',
        operationId: 'projects',
        tags: ['Project'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Projects',
                content: [
                    new OAT\JsonContent(
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Project')
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]  
    #[Route('/projects', name: 'api_get_projects', methods: ['GET'])]
    public function projects(): JsonResponse
    {
        $projects = $this->projects->findAllForApi();

        if ($projects === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Projects not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $projects,
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }


    #[OAT\Get(
        path: '/projects/{slug}',
        summary: 'Info for a specific project',
        operationId: 'project',
        tags: ['Project'],
        parameters: [new OAT\Parameter(ref: '#/components/parameters/slug')],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'A Project details',
                content: [
                    new OAT\JsonContent(
                        ref: '#/components/schemas/Project'
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
        ]
    )]
    #[Route('/projects/{slug}', name: 'api_get_project', methods: ['GET'])]
    public function project(Request $request): JsonResponse
    {
        $project = $this->projects->findOneForApi($request->get('slug'));

        if ($project === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Project not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $project,
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }
}