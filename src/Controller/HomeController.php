<?php

namespace App\Controller;

use App\Service\ConfigurationService;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ConfigurationService $config): Response
    {
        return $this->render('home/index.html.twig', [

        ]);
    }

    #[Route('/ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('home/ui.html.twig', [
            
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, SearchService $searchService): Response
    {
        return $this->render('home/search.html.twig', [
            'title' => 'Recherche',
            'search' => $searchService->search($request->query->get('q', ''), $request->query->getInt('page', 1), 50, 5)
        ]);
    }
}
