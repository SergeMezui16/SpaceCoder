<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangePasswordType;
use App\Authentication\Form\DeleteUserAccountType;
use App\Authentication\Form\EditProfileType;
use App\Authentication\Model\ChangePasswordModel;
use App\Entity\User;
use App\Service\AvatarUploaderService;
use App\Service\MailMakerService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private MailMakerService $mailer,
        private TokenStorageInterface $tokenStorage,
        private NotificationService $notifier
    ){}

    #[Route('/edit', name: 'profile_edit')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function edit(Request $request, AvatarUploaderService $uploader): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();
        $user = $auth->getUser();
        $oldAvatar = $user->getAvatar();

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            if($avatar === null){
                $user->setAvatar($oldAvatar);
            } else{
                $user->setAvatar($uploader->upload($avatar, $user->getPseudo()));
            }

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Vos informations ont bien été mis à jour !');

        }

        return $this->render('authentication/profile/edit.html.twig', [
            'title' => 'Editer le profile',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/change-password', name: 'profile_changepassword')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function changePassword(
        Request $request, 
        UserPasswordHasherInterface $encoder
    ): Response
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, new ChangePasswordModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $auth->setPassword($encoder->hashPassword($auth, $form->get('newPassword')->getData()));

            $this->manager->persist($auth);
            $this->manager->flush();

            $this->mailer->make($auth->getEmail(), 'Mot de passe changé avec succes', 'mail/auth/password_changed.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Mot de passe changé avec succes'
            ])->send();

            // Remove Session
            $request->getSession()->invalidate();
            $this->tokenStorage->setToken(null);

            $this->notifier->notifyOnPasswordChanged($auth->getUser());
            $this->addFlash('success', 'Mot de passe changé avec succes. Veuillez vous connecter avec vos nouveaux identifiants.');

            return $this->redirectToRoute('logout');

        }

        return $this->render('authentication/profile/change_password.html.twig', [
            'title' => 'Changer de mot de passe',
            'form' => $form->createView()
        ]);
    }


    #[Route('/delete', name: 'profile_delete')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function delete(
        Request $request, 
        EntityManagerInterface $entityManager, 
        EventDispatcherInterface $eventDispatcher, 
        RequestStack $requestStack, 
    ): Response
    {
        $form = $this->createForm(DeleteUserAccountType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            /** @var UserAuthentication $auth */
            $auth = $this->getUser();
            
            $auth->setDeleteAt(new \DateTimeImmutable('+30 days'));

            $entityManager->persist($auth);
            $entityManager->flush();

            $this->mailer->make($auth->getEmail(), 'Processus de suppression de compte lancée', 'mail/auth/delete_account.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Processus de suppression de compte lancée'
            ])->send();

            // $this->forceLogout($eventDispatcher, $this->tokenStorage, $requestStack);
            
            return $this->redirectToRoute('logout');
        }
        
        return $this->render('authentication/profile/delete.html.twig', [
            'title' => 'Suppression de compte',
            'form' => $form->createView()
        ]);
    }


    /**
     * Profile Route
     * 
     * Profile route has defined in routes.yaml
     * Cause that route must be more priority than others
     *
     * @param User $user
     * @return Response
     */
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(User $user): Response
    {
        return $this->render('authentication/profile/index.html.twig', [
            'title' => 'Mon profile',
            'user' => $user
        ]);
    }


    /**
     * Force Logout
     * 
     * @todo Make some tests
     *
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     * @return void
     */
    public function forceLogout(
        Request $request, 
        EventDispatcherInterface $eventDispatcher, 
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ): void 
    {
        // $logoutEvent = new LogoutEvent($request, $tokenStorage->getToken());
        // $eventDispatcher->dispatch($logoutEvent);
        // $tokenStorage->setToken(null);


        $response = new Response();
        $response->headers->clearCookie('REMEMBERME');
        $response->send();

        $logoutEvent = new LogoutEvent($requestStack->getCurrentRequest(), $tokenStorage->getToken());
        $eventDispatcher->dispatch($logoutEvent);
        $tokenStorage->setToken(null);
    }
}
