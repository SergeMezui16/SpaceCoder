<?php

namespace App\Authentication\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Authentication\Entity\UserAuthentication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentication/login.html.twig', [
            'last_email' => $lastUsername,
            'error'      => $error,
        ]);
    }

    #[Route('/loginsuccess', name: 'login_success')]
    public function onSuccessConnexion(Request $request, EntityManagerInterface $manager): Response
    {
        /**
         * @var UserAuthentication
         */
        $user = $this->getUser();

        if($user) {
            $user->load($request);
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute('home');
    }

}