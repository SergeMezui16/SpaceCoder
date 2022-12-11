<?php

namespace App\Controller;

use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('ui.html.twig', [
            
        ]);
    }
}
