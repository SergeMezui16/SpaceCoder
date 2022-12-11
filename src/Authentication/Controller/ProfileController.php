<?php

namespace App\Authentication\Controller;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Form\ChangePasswordType;
use App\Authentication\Form\DeleteUserAccountType;
use App\Authentication\Form\EditProfileType;
use App\Authentication\Form\Model\ChangePasswordModel;
use App\Entity\User;
use App\Service\AvatarUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private MailerInterface $mailer
    ){}

    #[Route('/edit', name: 'profile_edit')]
    public function edit(Request $request, AvatarUploaderService $uploader): Response
    {
        /** @var UserAuthentication */
        $auth = $this->getUser();
        $user = $auth->getUser();
        
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var UploadedFile $brochureFile */
            $avatar = $form->get('avatar')->getData();

            if($avatar){
                $user->setAvatar($uploader->upload($avatar, $user->getPseudo()));
            }

            $this->manager->persist($user);
            $this->manager->flush();

        }

        return $this->render('authentication/profile/edit.html.twig', [
            'title' => 'Editer le profile',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/change-password', name: 'profile_changepassword')]
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

            $email = new TemplatedEmail();
            $email->from(new Address('send@example.com'));
            $email->to(new Address($auth->getEmail()));
            $email->subject('Mot de passe changé avec succes');
            $email->htmlTemplate('mail/password_changed.html.twig');
            $email->context([
                'pseudo' => $auth->getUser()->getPseudo(),
                'emaila' => $auth->getEmail()
            ]);

            $this->mailer->send($email);

            $this->addFlash('info', 'Mot de passe changé avec succes.');

            return $this->redirectToRoute('profile', ['slug' => $auth->getUser()->getSlug()]);

        }

        return $this->render('authentication/profile/change_password.html.twig', [
            'title' => 'Changer de mot de passe',
            'form' => $form->createView(),
            'myPass' => 'rcb5mP7q8yxgtaY?'
        ]);
    }


    #[Route('/delete', name: 'profile_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $form = $this->createForm(DeleteUserAccountType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            /** @var UserAuthentication */
            $auth = $this->getUser();
            
            foreach ($auth->getUser()->getComments() as $comment) {
                $comment->setAuthor(null);
            }

            foreach ($auth->getUser()->getSuggestions() as $article) {
                $article->setSuggestedBy(null);
            }

            foreach ($auth->getUser()->getArticles() as $article) {
                $article->setAuthor(null);
            }

            $entityManager->flush();

            $entityManager->remove($auth->getUser());
            $entityManager->remove($auth);

            $entityManager->flush();
            
            // Remove Session
            $request->getSession()->invalidate();
            $tokenStorage->setToken();
            
            return $this->redirectToRoute('home');
        }
        
        return $this->render('authentication/profile/delete.html.twig', [
            'title' => 'Suppression de compte',
            'form' => $form->createView()
        ]);
    }

    public function index(User $user): Response
    {
        return $this->render('authentication/profile/index.html.twig', [
            'title' => 'Mon profile',
            'user' => $user
        ]);
    }
}
