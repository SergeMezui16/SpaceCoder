<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\RegistrationType;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailMakerService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


#[IsGranted('PUBLIC_ACCESS')]
#[Route('/register')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private UserRepository $userRepository,
        private MailMakerService $mailer,
        private NotificationService $notifier
    ) {
    }


    #[Route('', name: 'registration')]
    public function register(Request $request, UserPasswordHasherInterface $encoder): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $auth = $user->getAuth();

            $auth
                ->setBlocked(true)
                ->load($request)
                ->setPassword($encoder->hashPassword($auth, $auth->getPassword()));
            $user->setCoins(10);

            $this->userRepository->add($user, true);

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation',
                $user->getId(),
                $auth->getEmail(),
                ['id' => $user->getId()]
            );

            $this->mailer->make($auth->getEmail(), 'Confirmation d\'adresse', 'mail/auth/confirmation_email.html.twig', [
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'pseudo' => $user->getPseudo(),
                'expiration' => 1
            ])->send();

            return $this->redirectToRoute('registration_confirmation_ckeck', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('authentication/registration/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/email-check/{id}', name: 'registration_confirmation_ckeck')]
    public function checkEmail(User $user)
    {
        return $this->render('authentication/registration/check_email.html.twig', [
            'pseudo' => $user->getPseudo()
        ]);
    }



    #[Route('/verify', name: 'registration_confirmation')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $manager)
    {

        $id = $request->get('id');
        if ($id === null) return $this->redirectToRoute('home', ['source' => 'idnotfound_' . $id]);

        $user = $this->userRepository->find($id);
        if ($user === null) return $this->redirectToRoute('home', ['source' => 'usernotfound_' . $id]);

        $auth = $user->getAuth();

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $auth->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('registration');
        }

        $auth->setBlocked(false);
        $manager->persist($user);
        $manager->flush();

        $this->notifier->notifyOnWelcome($user);

        $this->mailer->make($auth->getEmail(), 'Bienvenu sur SpaceCoder', 'mail/auth/welcome.html.twig', [
            'subject' => 'Bienvenu sur SpaceCoder',
            'pseudo' => $user->getPseudo()
        ])->send();

        $this->addFlash('info', 'Votre adresse mail a bien été validé, veuillez vous connecter !');

        return $this->redirectToRoute('login');
    }
}
