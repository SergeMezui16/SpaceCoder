<?php

namespace App\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Ressource;
use App\Entity\Configuration;
use App\Authentication\Entity\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Authentication\Entity\UserAuthentication;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
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

        return $this->render('admin/index.html.twig', [
            'user' => new User()
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img class="m-2" width="15px"  src="favicon.svg" />SPACECODER ADMIN')
            ->setFaviconPath('favicon.svg')
            // ->setLocales(['fr'])        
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
            yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Role', 'fa fa-shield', Role::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Articles');
            yield MenuItem::linkToCrud('Article', 'fa fa-newspaper', Article::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Commentaire', 'fa fa-comment', Comment::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Projets');
            yield MenuItem::linkToCrud('Projet', 'fa fa-building', Project::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToCrud('Ressource', 'fas fa-database', Ressource::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Paramétrages');
            yield MenuItem::linkToCrud('Configuration', 'fa fa-toolbox', Configuration::class)->setPermission('ROLE_ADMIN');
            yield MenuItem::linkToUrl('Retourner au site', 'fa-solid fa-rotate-left', '/');
        
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        /**
         * @var UserAuthentication
         */
        $user = $this->getUser();
        
        return UserMenu::new()
            ->displayUserName()
            ->displayUserAvatar()
            ->setName($user->getUser())
            ->setAvatarUrl($user->getUser()->getAvatar())
            ->addMenuItems([
                // MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'link', ['param' => 'value']),
                // MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out')
            ])
        ;
    }
}
