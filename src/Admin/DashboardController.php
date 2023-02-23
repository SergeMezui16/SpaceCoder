<?php

namespace App\Admin;

use App\Authentication\Entity\ResetPasswordRequest;
use App\Authentication\Entity\Role;
use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Repository\UserAuthenticationRepository;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Configuration;
use App\Entity\Contact;
use App\Entity\Notification;
use App\Entity\Project;
use App\Entity\Ressource;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\ContactRepository;
use App\Repository\RessourceRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin',)]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UserRepository $users,
        private UserAuthenticationRepository $auths,
        private ArticleRepository $articles,
        private RessourceRepository $ressources,
        private CommentRepository $comments,
        private ContactRepository $contacts
    )
    {}
    
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //

        $all = $this->users->findAll();
        $newToday = 0;
        foreach ($all as $user) {
            if ((new \DateTimeImmutable())->getTimestamp() -  $user->getCreateAt()->getTimestamp() < 24*60*60) $newToday++;
        }

        return $this->render('admin/index.html.twig', [
            'nbUsers' => $this->users->count([]),
            'nbArticlesView' => $this->articles->views(),
            'nbNewToday' => $newToday,

            'contacts' => $this->contacts->findUndoneNb(),
            
            'bestArticles' => $this->articles->best(3),
            'bestRessources' => $this->ressources->best(3),
            
            'lastUsers' => $this->users->lastUsers(),
            'lastConnected' => $this->auths->lastConnected(),
            'lastComments' => $this->comments->last()
            
        ]);
    }


    // #[Route('/test', name: 'admin_test')]
    // public function test(): Response
    // {
        

    //     return $this->render('admin/test.html.twig', [
    //         'user' => new User()
    //     ]);
    // }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img class="m-2" width="15px"  src="/favicon.svg" />SPACECODER ADMIN')
            ->setFaviconPath('favicon.svg')
            // ->setLocales(['fr'])        
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
            yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Notification', 'fa fa-bell', Notification::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Role', 'fa fa-shield', Role::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Articles');
            yield MenuItem::linkToCrud('Article', 'fa fa-newspaper', Article::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Commentaire', 'fa fa-comment', Comment::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Projets');
            yield MenuItem::linkToCrud('Projet', 'fa fa-building', Project::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Ressource', 'fa fa-database', Ressource::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Autres');
            yield MenuItem::linkToCrud('Contact', 'fa fa-address-book', Contact::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('RPR', 'fas fa-key', ResetPasswordRequest::class)->setPermission('ROLE_ADMIN');
            
        yield MenuItem::section('Paramétrages');
            yield MenuItem::linkToCrud('Configuration', 'fa fa-toolbox', Configuration::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToUrl('Retourner au site', 'fa-solid fa-rotate-left', '/');

        yield MenuItem::section('Vers le site');
            yield MenuItem::linkToRoute('Articles', 'fas fa-newspaper', 'article');
            yield MenuItem::linkToRoute('Ressources', 'fas fa-database', 'ressources');
            yield MenuItem::linkToRoute('Projets', 'fas fa-building', 'project');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /**
         * @var UserAuthentication
         */
        $auth = $this->getUser();
        
        return UserMenu::new()
            ->displayUserName()
            ->displayUserAvatar()
            ->setName($auth->getUser())
            ->setAvatarUrl($auth->getUser()->getAvatar())
            ->addMenuItems([
                MenuItem::linkToRoute('Mon Profile', 'fa fa-id-card', 'profile', ['slug' => $auth->getUser()->getSlug()]),
                MenuItem::linkToRoute('Paramètres', 'fa fa-user-cog', 'profile_edit'),
                MenuItem::section(),
                MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out')
            ])
        ;
    }
}
