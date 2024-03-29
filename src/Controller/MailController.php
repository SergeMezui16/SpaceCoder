<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller is used to check render of Mail Twig Template
 */
#[Route('/mail')]
#[IsGranted('ROLE_ADMIN')]
class MailController extends AbstractController
{
    #[Route('/', name: 'mail')]
    public function index(): Response
    {
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }

    #[Route('/confirm-email', name: 'mail_confirm_email')]
    public function confirmEmail(): Response
    {
        return $this->render('mail/confirmation_email.html.twig', [
            'signedUrl' => 'https://spacecoder.fun/assets/images/logo/logo1.png',
            'pseudo' => 'Serge',
            'emaila' => 'serge@mezui.com',
            'expiration' => 288,
            'subject' => 'Confirmation d\'email' 
        ]);
    }

    #[Route('/reset-password', name: 'mail_reset_password')]
    public function resetPassword(): Response
    {
        return $this->render('mail/reset_password.html.twig', [
            'resetToken' => 4352,
            'subject' => 'Restauration de mot de passe'
        ]);
    }

    #[Route('/change-password', name: 'mail_change_password')]
    public function changePassword(): Response
    {
        return $this->render('mail/password_changed.html.twig', [
            'pseudo' => 'Serge',
            'subject' => 'Mot de passe changé avec succes',
            'domain' => 'http://localhost:8000'
        ]);
    }


    #[Route('/check-your-mail', name: 'mail_check')]
    public function checkYourMail(): Response
    {
        return $this->render('authentication/registration/check_email.html.twig', [
            'pseudo' => 'Serge',
            'subject' => 'Mot de passe changé avec succes',
            'domain' => 'http://localhost:8000'
        ]);
    }

    #[Route('/welcome', name: 'mail_welcome')]
    public function welcome(): Response
    {
        return $this->render('mail/auth/welcome.html.twig', [
            'pseudo' => 'Serge',
            'subject' => 'Bienvenu sur SpaceCoder',
        ]);
    }

    #[Route('/new-year', name: 'mail_new_year')]
    public function newYear(): Response
    {
        return $this->render('mail/command/happy_new_year.html.twig', [
            'pseudo' => 'Serge',
            'subject' => 'Bonne Année',
            'rho' => 20
        ]);
    }
}
