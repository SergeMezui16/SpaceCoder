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
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private MailMakerService $mailer,
        private TokenStorageInterface $tokenStorage
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
    public function changePassword(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        /** @var UserAuthentication */
        $auth = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, new ChangePasswordModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $auth->setPassword($encoder->hashPassword($auth, $form->get('newPassword')->getData()));

            $this->manager->persist($auth);
            $this->manager->flush();

            $this->mailer->make($auth->getEmail(), 'Mot de passe changé avec succes', 'mail/password_changed.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Mot de passe changé avec succes'
            ])->send();

            // Remove Session
            $request->getSession()->invalidate();
            $this->tokenStorage->setToken();

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
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DeleteUserAccountType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            /** @var UserAuthentication $auth */
            $auth = $this->getUser();
            
            $auth->setDeletedAt(new \DateTimeImmutable('+30 days'));

            $entityManager->persist($auth);
            $entityManager->flush();

            $this->mailer->make($auth->getEmail(), 'Processus de suppression de compte lancée', 'mail/delete_account.html.twig', [
                'pseudo' => $auth->getUser()->getPseudo(),
                'subject' => 'Processus de suppression de compte lancée'
            ])->send();
            
            return $this->redirectToRoute('logout');
        }
        
        return $this->render('authentication/profile/delete.html.twig', [
            'title' => 'Suppression de compte',
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('PUBLIC_ACCESS')]
    public function index(User $user): Response
    {
        return $this->render('authentication/profile/index.html.twig', [
            'title' => 'Mon profile',
            'user' => $user
        ]);
    }
}
