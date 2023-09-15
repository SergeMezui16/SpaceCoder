<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangePasswordFormType;
use App\Authentication\Form\ResetPasswordRequestFormType;
use App\Authentication\Model\ResetPasswordModel;
use App\Service\MailMakerService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
#[IsGranted('PUBLIC_ACCESS')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private MailMakerService $mailer,
        private NotificationService $notifier
    ) {}

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'reset_password_request')]
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render('authentication/reset_password/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'reset_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('authentication/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'reset_password_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('reset_password_reset');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var UserAuthentication $auth */
            $auth = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('reset_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class, new ResetPasswordModel());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $encodedPassword = $passwordHasher->hashPassword(
                $auth,
                $form->get('newPassword')->getData()
            );

            $auth->setPassword($encodedPassword);
            $this->entityManager->flush();

            $this->notifier->notifyOnResetPassword($auth->getUser());
            $this->mailer->make($auth->getEmail(), 'Mot de passe changé avec succes', 'mail/auth/password_changed.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Mot de passe changé avec succes'
            ])->send();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('logout');
        }

        return $this->render('authentication/reset_password/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Send an email to user to access to Password Reset form
     *
     * @param string $emailFormData
     * @return RedirectResponse
     */
    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $auth = $this->entityManager->getRepository(UserAuthentication::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        if (!$auth) {
            return $this->redirectToRoute('reset_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($auth);
        } catch (ResetPasswordExceptionInterface $e) {

            return $this->redirectToRoute('reset_check_email');
        }

        $this->mailer->make($auth->getEmail(), 'Votre demande de restauration de mot de passe', 'mail/auth/reset_password.html.twig', [
            'resetToken' => $resetToken,
            'subject' => 'Restauration de mot de passe'
        ])->send();

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('reset_check_email');
    }
}
