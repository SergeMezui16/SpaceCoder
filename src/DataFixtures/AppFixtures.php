<?php

namespace App\DataFixtures;

use App\Authentication\Entity\Role;
use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Configuration;
use App\Entity\Project;
use App\Entity\Ressource;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $fake;
    private $manager;
    private Role $adminRole;
    private Role $userRole;
    private Role $projectRole;
    /** @var User[] $users */
    private array $users;
    /** @var Article[] $articles */
    private array $articles;
    /** @var Comment[] $comments */
    private array $comments;
    /** @var Comment[] $replies */
    private array $replies;
    /** @var Project[] $projects */
    private array $projects;
    /** @var Ressource[] $ressources */
    private array $ressources;



    public function __construct(
        private UserPasswordHasherInterface $encoder
    ){
        $this->fake = (new Factory())::create('fr_FR');
        $this->users = [];
        $this->articles = [];
        $this->comments = [];
        $this->replies = [];
        $this->projects = [];
        $this->ressources = [];
    }
    
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        
        $this->setRoles();
        $this->setUser();
        $this->setUsers();
        $this->setArticles();
        $this->setComments();
        $this->setProjects();
        $this->setRessources();

        $manager->flush();
    }
    
    public function setConfigurations(): void
    {
        $this->manager->persist(
            (new Configuration())
                ->setName('APP_NAME')
                ->setValue('SPACECODER')
                ->setCategory('APP')
        );
        $this->manager->persist(
            (new Configuration())
                ->setName('APP_VERSION')
                ->setValue('3.0')
                ->setCategory('APP')
        );
    }

    public function setRoles(): void
    {
        $this->adminRole = (new Role())->setName('ROLE_ADMIN')->setContext('Administration')->setValid(true);
        $this->userRole = (new Role())->setName('ROLE_USER')->setContext('Utilisateur')->setValid(true);
        $this->projectRole = (new Role())->setName('ROLE_PROJECT_X')->setContext('Projet X')->setValid(true);

        $this->manager->persist($this->adminRole);
        $this->manager->persist($this->userRole);
        $this->manager->persist($this->projectRole);
    }

    public function setUser(): void
    {
        $myAuth = new UserAuthentication();
        $myPass = $this->encoder->hashPassword($myAuth, 'pass');

        $me = (new User())
            ->setAuth(
                $myAuth
                    ->setEmail('serge@mezui.com')
                    ->setPassword($myPass)
                    ->setBlocked(false)
                    ->setRole($this->adminRole)
            )
            ->setPseudo('Serge Mezui')
            ->setSlug('SergeMezui')
            ->setCountry('GA')
            ->setCoins(1000)
            ->setBornAt(new \DateTimeImmutable('2002-10-04 17:24:43.000000'));

        $this->manager->persist($myAuth);
        $this->manager->persist($me);
    }

    public function setUsers()
    {
        for ($i = 0; $i < 15; $i++) {

            $auth = new UserAuthentication();
            $pass = $this->encoder->hashPassword($auth, 'pass');

            $auth
                ->setEmail($this->fake->email())
                ->setPassword($pass)
                ->setBlocked($this->fake->boolean())
                ->setRole($this->userRole);

            $this->manager->persist($auth);

            $this->users[] = $user = (new User())
                ->setAuth($auth)
                ->setSlug('slugUser' . $i)
                ->setPseudo($this->fake->name())
                ->setCountry($this->fake->countryCode())
                ->setCoins(10)
                ->setBornAt(new \DateTimeImmutable());

            $this->manager->persist($user);
        }
    }

    public function setArticles()
    {
        for ($i = 0; $i < 30; $i++) {
            $this->articles[] = $article = (new Article())
                ->setTitle($this->fake->sentence())
                ->setSubject($this->fake->sentence(2))
                ->setDescription($this->fake->paragraph())
                ->setContent($this->fake->text(1000))
                ->setViews($this->fake->numberBetween(0, 200))
                ->setImage($this->fake->imageUrl(640, 350))
                ->setAuthor($this->fake->randomElement([...$this->users, null]))
                ->setSuggestedBy($this->fake->randomElement([...$this->users, null]))
                ->setAuthor($this->fake->randomElement($this->users))
                ->setLevel($this->fake->randomElement([1, 2, 3]))
                ->setPublishedAt(new DateTimeImmutable());
            $this->manager->persist($article);
        }
    }

    public function setComments()
    {
        for ($i = 0; $i < 30; $i++) {
            $this->comments[] = $comment = (new Comment())
                ->setArticle($this->fake->randomElement($this->articles))
                ->setAuthor($this->fake->randomElement([...$this->users, null]))
                ->setContent($this->fake->sentence(10, true));

            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $this->replies[] = $reply = (new Comment())
                    ->setArticle($comment->getArticle())
                    ->setAuthor($this->fake->randomElement([...$this->users, null]))
                    ->setContent($this->fake->sentence(10, true));
                $comment->setReplyTo($reply);
            }

            $this->manager->persist($comment);
            $this->manager->persist($reply);
        }
    }

    public function setProjects()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->projects[] = $project = (new Project())
                ->setName($this->fake->sentence(2))
                ->setDescription($this->fake->paragraph(1))
                ->setVisit($this->fake->numberBetween(0, 100))
                ->setImage($this->fake->imageUrl(640, 350))
                ->setAuthors($this->fake->sentence(2))
                ->setUrl($this->fake->url())
                ->setRole($this->projectRole);
            $this->manager->persist($project);
        }
    }

    public function setRessources()
    {
        for ($i = 0; $i < 70; $i++) {
            $this->ressources[] = $ressource = (new Ressource())
                ->setName($this->fake->sentence(2))
                ->setImage($this->fake->imageUrl(640, 350))
                ->setDescription($this->fake->paragraph(1))
                ->setClicks($this->fake->numberBetween(0, 100))
                ->setLink($this->fake->url())
                ->setCategories($this->fake->words());
            $this->manager->persist($ressource);
        }
    }
}
