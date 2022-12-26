<?php

namespace App\Controller;

use App\Service\ConfigurationService;
use App\Service\SearchService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ConfigurationService $config, MailerInterface $mailer): Response
    {
        
        return $this->render('home/index.html.twig', [

        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, SearchService $searchService): Response
    {
        return $this->render('home/search.html.twig', [
            'title' => 'Recherche',
            'search' => $searchService->search($request->query->get('q', ''), $request->query->getInt('page', 1), 50, 10)
        ]);
    }
    

    #[Route('/terms-and-conditions', name: 'terms')]
    public function terms(): Response
    {
        return $this->render('home/terms_and_conditions.html.twig', [
            'title' => 'Conditions d\'utilisation'
        ]);
    }
}
