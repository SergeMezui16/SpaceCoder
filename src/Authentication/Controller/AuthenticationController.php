<?php

namespace App\Authentication\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{

    #[Route('/login', name: 'authentication_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('authentication/login.html.twig', []);
    }

    #[Route('/loginsuccess', name: 'authentication_login_success')]
    public function onSuccessConnexion(Request $request, EntityManagerInterface $repo): Response
    {
        /**
         * @var UserAuthentication
         */
        $user = $this->getUser();

        if($user) {
            $user->addIp($request->server->get('REMOTE_ADDR'));
            $user->setLastConnexion(new \DateTimeImmutable());
            if($user->isFirstConnexion()) $user->setFirstConnexion(new \DateTimeImmutable());
        }

        $repo->persist($user);
        $repo->flush();

        return $this->redirectToRoute('app_home');
    }

}