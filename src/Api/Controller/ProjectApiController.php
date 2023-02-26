<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ProjectRepository;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectApiController extends AbstractApiController
{
    public function __construct(
        private ProjectRepository $projects
    ) {}

    /**
     * @OA\Get(
     *     path="/projects",
     *     summary="List all projects",
     *     operationId="projects",
     *     tags={"Project"},
     *     @OA\Response(
     *         response="200",
     *         description="List of Projects",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Project") 
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    #[Route('/projects', name: 'api_get_projects', methods: ['GET'])]
    public function projects(): JsonResponse
    {
        $projects = $this->projects->findAll();

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

    /**
     * @OA\Get(
     *     path="/project/{slug}",
     *     summary="Info for a specific project",
     *     operationId="project",
     *     tags={"Project"},
     *     @OA\Parameter(ref="#/components/parameters/slug"),
     *     @OA\Response(
     *         response="200",
     *         description="A Project details",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     )
     * )
     */
    #[Route('/project/{slug}', name: 'api_get_project', methods: ['GET'])]
    public function project(Request $request): JsonResponse
    {
        $project = $this->projects->findOneBy(['slug' => $request->get('slug')]);

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
