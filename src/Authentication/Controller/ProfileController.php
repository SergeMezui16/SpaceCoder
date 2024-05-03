<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangeEmailType;
use App\Authentication\Form\ChangePasswordType;
use App\Authentication\Form\DeleteUserAccountType;
use App\Authentication\Form\EditProfileType;
use App\Authentication\Model\ChangePasswordModel;
use App\Authentication\Repository\UserAuthenticationRepository;
use App\Entity\User;
use App\Service\AvatarUploaderService;
use App\Service\MailMakerService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/profile', name: 'profile.')]
class ProfileController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface       $manager,
        private readonly MailMakerService             $mailer,
        private readonly TokenStorageInterface        $tokenStorage,
        private readonly NotificationService          $notifier,
        private readonly VerifyEmailHelperInterface   $verifyEmailHelper,
        private readonly UserAuthenticationRepository $userRepository,
    )
    {
    }

    /**
     * Profile Route
     *
     * Profile route has defined in /config/routes.yaml
     * Cause that route must be more priority than others
     *
     * @param User $user
     * @return Response
     */
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(User $user): Response
    {
        return $this->render('pages/authentication/profile/index.html.twig', [
            'title' => 'Mon profile',
            'user' => $user
        ]);
    }

    #[Route('/edit', name: 'edit', methods: ['POST', 'GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, AvatarUploaderService $uploader): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();
        $user = $auth->getUser();
        $oldAvatar = $user->getAvatar();

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            if ($avatar === null) {
                $user->setAvatar($oldAvatar);
            } else {
                $user->setAvatar($uploader->upload($avatar, $user->getPseudo()));
            }

            $this->manager->flush();

            $this->addFlash('success', 'Vos informations ont bien été mis à jour !');

            return $this->redirectToRoute('profile', [
                'slug' => $auth->getUser()->getSlug()
            ]);
        }

        return $this->render('pages/authentication/profile/edit.html.twig', [
            'title' => 'Éditer le profile',
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/change-password', name: 'password', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(
        Request                     $request,
        UserPasswordHasherInterface $encoder
    ): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, new ChangePasswordModel());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $auth->setPassword($encoder->hashPassword($auth, $form->get('newPassword')->getData()));

            $this->manager->persist($auth);
            $this->manager->flush();

            $this->mailer->make($auth->getEmail(), 'Mot de passe changé avec succès', 'mail/auth/password_changed.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Mot de passe changé avec succès'
            ])->send();

            // Remove Session
            $request->getSession()->invalidate();
            $this->tokenStorage->setToken(null);

            $this->notifier->notifyOnPasswordChanged($auth->getUser());
            $this->addFlash('success', 'Mot de passe changé avec succès. Veuillez vous connecter avec vos nouveaux identifiants.');

            return $this->redirectToRoute('logout');
        }

        return $this->render('pages/authentication/profile/change_password.html.twig', [
            'title' => 'Changer de mot de passe',
            'form' => $form
        ]);
    }


    #[Route('/new-email', name: 'email', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function email(Request $request): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        $form = $this->createForm(ChangeEmailType::class, $auth);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'profile.email.confirm',
                $auth->getUser()->getId(),
                $auth->getEmail()
            );

            $auth->setBlocked(true);
            $this->manager->flush();

            $this->mailer->make($auth->getEmail(), 'Adresse email changée', 'mail/auth/confirmation_new_email.html.twig', [
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'pseudo' => $auth->getUser()->getPseudo(),
                'expiration' => 1
            ])->send();

            return $this->redirectToRoute('profile.email.check');
        }

        return $this->render('pages/authentication/change_email/index.html.twig', [
            'form' => $form,
            'title' => 'Changer d\'adresse mail'
        ]);
    }

    #[Route('/email-check', name: 'email.check')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function checkEmail(): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        return $this->render('pages/authentication/change_email/check_email.html.twig', [
            'pseudo' => $auth->getPseudo()
        ]);
    }


    #[Route('/email-changed', name: 'email.confirm')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $auth->getUser()->getId(), $auth->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('profile.email');
        }

        $auth->setBlocked(false);
        $this->manager->flush();

        $this->addFlash('info', 'Votre adresse mail a bien été validé, veuillez vous connecter !');

        return $this->redirectToRoute('logout');
    }

    #[Route('/delete', name: 'delete', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(
        Request                $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(DeleteUserAccountType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserAuthentication $auth */
            $auth = $this->getUser();

            $auth->setDeleteAt(new \DateTimeImmutable('+30 days'));
            $entityManager->flush();

            $this->mailer->make($auth->getEmail(), 'Processus de suppression de compte lancée', 'mail/auth/delete_account.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Processus de suppression de compte lancée'
            ])->send();

            return $this->redirectToRoute('logout');
        }

        return $this->render('pages/authentication/profile/delete.html.twig', [
            'title' => 'Suppression de compte',
            'form' => $form
        ]);
    }
}
