<?php

namespace App\Controller;

use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/DevSpace')]
class DevSpaceController extends AbstractController
{
    #[Route('/', name: 'dev_space')]
    public function index(ConfigurationService $config): Response
    {
        return $this->render('dev_space/index.html.twig', [
            'title' => 'DevSpace',
            'arl' => $config->get('ARL')
        ]);
    }

    #[Route('/ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('dev_space/ui.html.twig', []);
    }
}
