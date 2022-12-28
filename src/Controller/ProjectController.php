<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\EntityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'project')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'title' => 'Projets',
            'projects' => $projectRepository->findAll()
        ]);
    }

    #[Route('/{slug}', name: 'project_view')]
    public function view(Project $project, EntityService $entityService): Response
    {
        $entityService->incrementProjectVisits($project);
        return $this->redirect($project->getUrl());
    }
}
