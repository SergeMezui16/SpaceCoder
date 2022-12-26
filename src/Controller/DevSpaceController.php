<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/DevSpace')]
class DevSpaceController extends AbstractController
{
    #[Route('/', name: 'dev_space')]
    public function index(): Response
    {
        return $this->render('dev_space/index.html.twig', [
            'title' => 'DevSpace',
        ]);
    }

    #[Route('/ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('dev_space/ui.html.twig', []);
    }
}
