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

    public function __construct(private UserPasswordHasherInterface $encoder){}
    
    public function load(ObjectManager $manager): void
    {
        $fake = (new Factory())::create('fr_FR');

        // Configuration
        $manager->persist(
            (new Configuration())
                ->setName('APP_NAME')
                ->setValue('SPACECODER')
                ->setCategory('APP')
        );
        $manager->persist(
            (new Configuration())
                ->setName('APP_VERSION')
                ->setValue('3.0')
                ->setCategory('APP')
        );
            
        


        // ROLES
        $adminRole = (new Role())->setName('ROLE_ADMIN')->setContext('Administration')
            ->setValid(true);

        $userRole = (new Role())->setName('ROLE_USER')->setContext('Utilisateur')
            ->setValid(true);

        $projectRole = (new Role())->setName('ROLE_PROJECT_X')->setContext('Projet X')
            ->setValid(true);

        $roles = [$adminRole, $userRole];
        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($projectRole);

        // ME
        $myAuth = new UserAuthentication();
        $myPass = $this->encoder->hashPassword($myAuth, 'pass');

        $me = (new User())
            ->setAuth(
               $myAuth
               ->setEmail('serge@mezui.com')
               ->setPassword($myPass)
               ->setBlocked(false)
               ->addRole($adminRole)
               ->addRole($userRole)
               ->addRole($projectRole)
            )
            ->setPseudo('Serge Mezui')
            ->setSlug('SergeMezui')
            ->setCountry('GA')
            ->setCoins(1000)
            ->setBornAt(new \DateTimeImmutable('2002-10-04 17:24:43.000000'));

        $manager->persist($myAuth);
        $manager->persist($me);

        // USERS
        $users = [];
        for ($i = 0; $i < 15; $i++) {

            $auth = new UserAuthentication();
            $pass = $this->encoder->hashPassword($auth, 'pass');

            $auth
                ->setEmail($fake->email())
                ->setPassword($pass)
                ->setBlocked($fake->boolean())
                ->addRole($userRole);

            $manager->persist($auth);

            $users[] = $user = (new User())
                ->setAuth($auth)
                ->setSlug('slugUser' . $i)
                ->setPseudo($fake->name())
                ->setCountry($fake->countryCode())
                ->setCoins(10)
                ->setBornAt(new \DateTimeImmutable());

            $manager->persist($user);
        }

        // ARTICLE
        $articles = [];
        for ($i=0; $i < 30; $i++) { 
            $articles[] = $article = (new Article())
                ->setTitle($fake->sentence())
                ->setSubject($fake->sentence(2))
                ->setDescription($fake->paragraph())
                ->setContent($fake->text(1000))
                ->setViews($fake->numberBetween(0, 200))
                ->setImage($fake->imageUrl(640, 350))
                ->setSuggestedBy($fake->randomElement($users))
                ->setAuthor($fake->randomElement($users))
                ->setLevel($fake->randomElement([1, 2, 3]))
                ->setPublishedAt(new DateTimeImmutable())
            ;
            $manager->persist($article);
        }


        // COMMENT
        $comments = [];
        for ($i=0; $i < 30; $i++) { 
            $comments[] = $comment = (new Comment())
                ->setArticle($fake->randomElement($articles))
                ->setAuthor($fake->randomElement($users))
                ->setReplyTo($fake->randomElement([$fake->randomElement($comments), null]))
                ->setContent($fake->sentence(10, true))
            ;
            $manager->persist($comment);
        }

        // PROJECT
        $projects = [];
        for ($i=0; $i < 10; $i++) { 
            $projects[] = $project = (new Project())
                ->setName($fake->sentence(2))
                ->setDescription($fake->paragraph(1))
                ->setVisit($fake->numberBetween(0, 100))
                ->setImage($fake->imageUrl(640, 350))
                ->setAuthors($fake->sentence(2))
                ->setRole($projectRole)
            ;
            $manager->persist($project);
        }

        // RESSOURCE
        $ressources = [];
        for ($i=0; $i < 70; $i++) { 
            $ressources[] = $ressource = (new Ressource())
                ->setName($fake->sentence(2))
                ->setImage($fake->imageUrl(640, 350))
                ->setDescription($fake->paragraph(1))
                ->setClicks($fake->numberBetween(0, 100))
                ->setLink($fake->url())
                ->setCategories($fake->words())
            ;
            $manager->persist($ressource);
        }

        $manager->flush();
    }
}
