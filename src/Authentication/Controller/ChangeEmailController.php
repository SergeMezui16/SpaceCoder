<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangeEmailType;
use App\Authentication\Repository\UserAuthenticationRepository;
use App\Service\MailMakerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ChangeEmailController extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper, 
        private UserAuthenticationRepository $userRepository,
        private EntityManagerInterface $manager,
        private MailMakerService $mailer
    )
    {}

    
    #[Route('/new-email', name: 'change_email')]
    public function change(Request $request): Response
    {
        /** @var UserAuthentication */
        $auth = $this->getUser();
        
        $form = $this->createForm(ChangeEmailType::class, $auth);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'change_confirmation',
                $auth->getUser()->getId(),
                $auth->getEmail()
            );

            $auth->setBlocked(true);

            $this->manager->persist($auth);
            $this->manager->flush();

            $this->mailer->make($auth->getEmail(), 'Confirmation d\'adresse', 'mail/auth/confirmation_email.html.twig', [
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'pseudo' => $auth->getUser()->getPseudo(),
                'expiration' => 1
            ])->send();

            return $this->render('authentication/change_email/check_email.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo()
            ]);
        }

        return $this->render('authentication/change_email/index.html.twig', [
            'form' => $form->createView(),  
            'title' => 'Changer d\'adresse mail'
        ]);
    }



    #[Route('/email-changed', name: 'change_confirmation')]
    public function verifyUserEmail(Request $request)
    {
        /** @var UserAuthentication */
        $auth = $this->getUser();

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $auth->getUser()->getId(), $auth->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('change_email');
        }

        $auth->setBlocked(false);
        $this->manager->persist($auth);
        $this->manager->flush();

        $this->addFlash('info', 'Votre adresse mail a bien été validé, veuillez vous connecter !');

        return $this->redirectToRoute('logout');
    }
}
