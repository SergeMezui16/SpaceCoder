<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangeEmailType;
use App\Authentication\Repository\UserAuthenticationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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
        private MailerInterface $mailer
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
            
            $email = new TemplatedEmail();
            $email->from(new Address('send@example.com'));
            $email->to(new Address($auth->getEmail()));
            $email->subject('Confirmation d\'adresse');
            $email->htmlTemplate('mail/confirmation_email.html.twig');
            $email->context([
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'pseudo' => $auth->getUser()->getPseudo(),
                'emaila' => $auth->getEmail(),
                'expiration' => 1
            ]);

            $auth->setBlocked(true);

            $this->manager->persist($auth);
            $this->manager->flush();
            
            $this->mailer->send($email);

            return $this->render('authentication/check_email.html.twig');
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
