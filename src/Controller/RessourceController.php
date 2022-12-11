<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Repository\ArticleRepository;
use App\Repository\RessourceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ressource')]
class RessourceController extends AbstractController
{
    #[Route('/', name: 'ressource')]
    public function index(PaginatorInterface $paginator, Request $request, RessourceRepository $ressourceRepository): Response
    {
        $pagination = $paginator->paginate(
            $ressourceRepository->findAllQuery($request->query->get('q', '')),
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('ressource/index.html.twig', [
            'title' => 'Ressources',
            'pagination' => $pagination
        ]);
    }

    #[Route('/{slug}', name: 'ressource_visit')]
    public function visit(Ressource $ressource)
    {
        
        return $this->redirect($ressource->getLink());
    }
}
