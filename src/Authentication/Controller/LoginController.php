<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[IsGranted('PUBLIC_ACCESS')]
class LoginController extends AbstractController
{

    use TargetPathTrait;

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $this->saveTargetPath($request->getSession(), 'main', $request->server->get('HTTP_REFERER', $this->generateUrl('home')));

        return $this->render('authentication/login.html.twig', [
            'last_email' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
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