<?php

namespace App\Admin;

use App\Admin\Controller\ParishCrudController;
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
use App\Repository\DioceseRepository;
use App\Repository\ParishRepository;
use App\Repository\RessourceRepository;
use App\Repository\ServantRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneRepository;
use App\Servant\Entity\Diocese;
use App\Servant\Entity\Parish;
use App\Servant\Entity\ParishStatus;
use App\Servant\Entity\Servant;
use App\Servant\Entity\ServantLevel;
use App\Servant\Entity\ServantPost;
use App\Servant\Entity\Zone;
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
        private ContactRepository $contacts,
        private DioceseRepository $dioceses,
        private ZoneRepository $zones,
        private ParishRepository $parishes,
        private ServantRepository $servants
    ) {
    }

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
            if ((new \DateTimeImmutable())->getTimestamp() -  $user->getCreatedAt()->getTimestamp() < 24 * 60 * 60) $newToday++;
        }

        return $this->render('admin/index.html.twig', [
            'nbUsers' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->users->count([]) : 0,
            'nbArticlesView' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->articles->views() : 0,
            'nbNewToday' => $this->isGranted('ROLE_SUPER_ADMIN') ? $newToday : 0,

            'contacts' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->contacts->findUndoneNb() : 0,

            'bestArticles' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->articles->best(3) : [],
            'bestRessources' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->ressources->best(3) : [],

            'lastUsers' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->users->lastUsers() : [],
            'lastConnected' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->auths->lastConnected() : [],
            'lastComments' => $this->isGranted('ROLE_SUPER_ADMIN') ? $this->comments->last() : [],

            'zones' => $this->isGranted('ROLE_PROJECT_SERVANT') ? $this->zones->count([]) : 0,
            'dioceses' => $this->isGranted('ROLE_PROJECT_SERVANT') ? $this->dioceses->count([]) : 0,
            'parishes' => $this->isGranted('ROLE_PROJECT_SERVANT') ? $this->parishes->count([]) : 0,
            'servants' => $this->isGranted('ROLE_PROJECT_SERVANT') ? $this->servants->count([]) : 0,

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

        yield MenuItem::section('Utilisateurs')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Notification', 'fa fa-bell', Notification::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Role', 'fa fa-shield', Role::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Articles')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Article', 'fa fa-newspaper', Article::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Commentaire', 'fa fa-comment', Comment::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Projets')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Projet', 'fa fa-building', Project::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Ressource', 'fa fa-database', Ressource::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Projet Servant');
        yield MenuItem::linkToCrud('Diocèse', 'fa fa-map-location', Diocese::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Zone', 'fa fa-location-dot', Zone::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Paroisse', 'fa fa-church', Parish::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Servant', 'fa fa-user', Servant::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Niveau', 'fa fa-list', ServantLevel::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Poste', 'fa fa-list', ServantPost::class)->setPermission('ROLE_PROJECT_SERVANT');
        yield MenuItem::linkToCrud('Statut', 'fa fa-list', ParishStatus::class)->setPermission('ROLE_PROJECT_SERVANT');

        yield MenuItem::section('Autres')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Contact', 'fa fa-address-book', Contact::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('RPR', 'fas fa-key', ResetPasswordRequest::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Paramétrages')->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Configuration', 'fa fa-toolbox', Configuration::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Vers le site');
        yield MenuItem::linkToUrl('Retourner au site', 'fa-solid fa-rotate-left', '/');
        yield MenuItem::linkToRoute('Articles', 'fas fa-newspaper', 'article');
        yield MenuItem::linkToRoute('Ressources', 'fas fa-database', 'ressource');
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
            ]);
    }
}
