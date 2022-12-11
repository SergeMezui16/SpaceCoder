<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\Entity\ProjectService;
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
    public function view(Project $project, ProjectService $projectService): Response
    {
        $projectService->incrementVisit($project);
        return $this->redirectToRoute('project'); // Redirect to its url
    }
}
