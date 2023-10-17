<?php

namespace App\Servant\Controller;

use App\Repository\DioceseRepository;
use App\Servant\Entity\Diocese;
use App\Servant\Entity\Parish;
use App\Servant\Entity\Servant;
use App\Servant\Entity\Zone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/servant')]
class ServantController extends AbstractController
{
    #[Route('/', name: 'servant')]
    public function index(DioceseRepository $dioceseRepository): Response
    {
        return $this->render('servant/index.html.twig', [
            'dioceses' => $dioceseRepository->findAll()
        ]);
    }

    #[Route('/dioceses/{id}', name: 'servant_diocese')]
    public function diocese(Diocese $diocese): Response
    {
        return $this->render('servant/diocese.html.twig', [
            'diocese' => $diocese
        ]);
    }

    #[Route('/zones/{id}', name: 'servant_zone')]
    public function zone(Zone $zone): Response
    {
        return $this->render('servant/zone.html.twig', [
            'zone' => $zone
        ]);
    }

    #[Route('/parishes/{id}', name: 'servant_parish')]
    public function parish(Parish $parish): Response
    {
        return $this->render('servant/parish.html.twig', [
            'parish' => $parish,
            'bureau' => $parish->getServants()->filter(fn ($s) => $s->getPost() !== null),
            'servants' => $parish->getServants()->filter(fn ($s) => $s->getPost() === null),
        ]);
    }

    #[Route('/servants/{id}', name: 'servant_servant')]
    public function servant(Servant $servant): Response
    {
        return $this->render('servant/servant.html.twig', [
            'servant' => $servant
        ]);
    }
}
