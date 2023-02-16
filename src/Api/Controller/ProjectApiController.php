<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ProjectApiController extends AbstractApiController
{
    public function __construct(
        private ProjectRepository $projects
    ) {}
    
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
