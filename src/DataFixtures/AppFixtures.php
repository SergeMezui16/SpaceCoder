<?php

namespace App\DataFixtures;

use App\Authentication\Entity\Role;
use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Configuration;
use App\Entity\Notification;
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

    public function __construct(
        private UserPasswordHasherInterface $encoder
    ){}
    
    public function load(ObjectManager $manager): void
    {

        $fake = (new Factory())::create('fr_FR');
        $users = [];
        $articles = [];
        $comments = [];
        $replies = [];
        $projects = [];
        $ressources = [];
        $notifications = [];

        echo(' Configuration ');
        $manager->persist(
            (new Configuration())
                ->setName('APP_NAME')
                ->setValue('SPACECODER')
                ->setCategory('APP')
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'))
        );
        $manager->persist(
            (new Configuration())
                ->setName('APP_VERSION')
                ->setValue('3.0')
                ->setCategory('APP')
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'))
        );
        $manager->persist(
            (new Configuration())
                ->setName('ARL')
                ->setValue('AZERTYUIOPMLKJHGFDSQWXCVBNJKLMPOIUYTREZAQSDFGHJKLKJHNBVCXSZEDFTGHUJ')
                ->setCategory('APP')
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'))
        );
        echo('** ✅');

        echo ("\n Role ");
        $adminRole = (new Role())->setName('ROLE_ADMIN')->setContext('Administration')->setValid(true);
        $userRole = (new Role())->setName('ROLE_USER')->setContext('Utilisateur')->setValid(true);
        $projectRole = (new Role())->setName('ROLE_PROJECT_X')->setContext('Projet X')->setValid(true);

        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($projectRole);
        echo ('*** ✅');


        echo ("\n User ");
        $myAuth = new UserAuthentication();
        $myPass = $this->encoder->hashPassword($myAuth, 'pass');

        $me = (new User())
            ->setAuth(
                $myAuth
                    ->setEmail('serge@mezui.com')
                    ->setPassword($myPass)
                    ->setBlocked(false)
                    ->setRole($adminRole)
                    ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                    ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'))
            )
            ->setPseudo('Serge Mezui')
            ->setSlug('SergeMezui')
            ->setCountry('GA')
            ->setCoins(1000)
            ->setBornAt(new \DateTimeImmutable('- ' . mt_rand(2000000, 30000000) . ' second'))
            ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
            ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));

        $manager->persist($myAuth);
        $manager->persist($me);
        
        echo ('* ✅');

        echo ("\n Users ");
        for ($i = 0; $i < 5; $i++) {

            $auth = new UserAuthentication();
            $pass = $this->encoder->hashPassword($auth, 'pass');

            $auth
                ->setEmail($fake->email())
                ->setPassword($pass)
                ->setBlocked($fake->boolean())
                ->setRole($userRole)
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));

            $manager->persist($auth);

            $users[] = $user = (new User())
                ->setAuth($auth)
                ->setSlug('slug-user' . $i)
                ->setPseudo($fake->name())
                ->setCountry($fake->countryCode())
                ->setCoins(10)
                ->setBornAt(new \DateTimeImmutable('- ' . mt_rand(2000000, 30000000) . ' second'))
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));

            $manager->persist($user);
            echo ('*');
        }

        echo (' ✅');

        echo ("\n Article ");
        for ($i = 0; $i < 5; $i++) {
            $articles[] = $article = (new Article())
                ->setTitle($fake->sentence())
                ->setSubject($fake->sentence(2))
                ->setDescription($fake->paragraph())
                ->setContent($fake->text(1000))
                ->setViews($fake->numberBetween(0, 200))
                ->setImage($fake->imageUrl(640, 350))
                ->setAuthor($fake->randomElement([...$users, $me, null]))
                ->setSuggestedBy($fake->randomElement([...$users, $me, null]))
                ->setLevel($fake->randomElement([1, 2, 3]))
                ->setPublishedAt(new DateTimeImmutable('- ' . mt_rand(1, 30000 * ($i + 1)) . ' second'))
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000 * ($i + 1)) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000 * ($i + 1)) . ' second'));
            $manager->persist($article);
            echo ('*');
        }
        echo (' ✅');

        echo ("\n Comment ");
        for ($i = 0; $i < 10; $i++) {
            $comments[] = $comment = (new Comment())
                ->setArticle($fake->randomElement($articles))
                ->setAuthor($fake->randomElement([...$users, $me, null]))
                ->setContent($fake->sentence(10, true))
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));
            
            for ($j = 0; $j <= mt_rand(0, 2); $j++) {
                $replies[] = $reply = (new Comment())
                    ->setArticle($comment->getArticle())
                    ->setAuthor($fake->randomElement([...$users, $me, null]))
                    ->setContent($fake->sentence(10, true))
                    ->setReplyTo($comment)
                    ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                    ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));
                echo ('*');
                    
                $manager->persist($reply);
                $comment->addReply($reply);
            }
            
            $manager->persist($comment);
            echo ('*');
        }
        echo (' ✅');

        
        echo ("\n Project ");
        for ($i = 0; $i < 5; $i++) {
            $projects[] = $project = (new Project())
                ->setName($fake->sentence(2))
                ->setDescription($fake->paragraph(1))
                ->setVisit($fake->numberBetween(0, 100))
                ->setImage($fake->imageUrl(640, 350))
                ->setAuthors($fake->sentence(2))
                ->setUrl($fake->url())
                ->setRole($projectRole)
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));
            $manager->persist($project);
            echo ('*');
        }
        echo (' ✅');

        
        echo ("\n Ressource ");
        for ($i = 0; $i < 20; $i++) {
            $ressources[] = $ressource = (new Ressource())
                ->setName($fake->sentence(2))
                ->setImage($fake->imageUrl(640, 350))
                ->setDescription($fake->paragraph(1))
                ->setClicks($fake->numberBetween(0, 100))
                ->setLink($fake->url())
                ->setCategories($fake->words())
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));
            $manager->persist($ressource);
            echo ('*');
        }
        echo (' ✅');

        
        echo ("\n Notification ");
        for ($i = 0; $i < 15; $i++) {
            $notifications[] = $notification = (new Notification())
                ->setTitle($fake->sentence(2))
                ->setContent($fake->paragraph(1))
                ->addRecipient($fake->randomElement([...$users, $me]))
                ->setAction('/article')
                ->setHeader($fake->randomElement(['comment', 'account', 'gift', 'new']))
                ->setSentAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setCreateAt(new \DateTimeImmutable('- ' . mt_rand(1, 300000) . ' second'))
                ->setUpdateAt(new \DateTimeImmutable('- ' . mt_rand(1, 100000) . ' second'));
            $manager->persist($notification);
            echo ('*');
        }
        echo (' ✅');


        echo ("\n Flushing");
        $manager->flush();
    }
}
