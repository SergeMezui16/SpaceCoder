<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Configuration;
use App\Authentication\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Authentication\Entity\UserAuthentication;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $encoder){}
    
    public function load(ObjectManager $manager): void
    {
        $fake = (new Factory())::create('fr_FR');

        // Configuration
        $appName = (new Configuration())
            ->setName('APP_NAME')
            ->setValue('SPACECODER')
            ->setCategory('APP')
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());
        $appVersion = (new Configuration())
            ->setName('APP_VERSION')
            ->setValue('3.0')
            ->setCategory('APP')
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());


        // ROLES
        $adminRole = (new Role())->setName('ROLE_ADMIN')->setContext('Administration')
            ->setValid(true)
            // ->setCreateAt(new \DateTimeImmutable())
            // ->setUpdateAt(new \DateTimeImmutable())
            ;

        $userRole = (new Role())->setName('ROLE_USER')->setContext('Utilisateur')
            ->setValid(true)
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());

        $projectRole = (new Role())->setName('ROLE_PROJECT_X')->setContext('Projet X')
            ->setValid(true)
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());


        $manager->persist($adminRole);
        $manager->persist($userRole);
        $manager->persist($projectRole);

        // ME
        $myAuth = new UserAuthentication();
        $myPass = $this->encoder->hashPassword($myAuth, 'pass');
    
        $myAuth
            ->setEmail('serge@mezui.com')
            ->setPassword($myPass)
            ->setBlocked(false)
            ->addRole($adminRole)
            ->addRole($userRole)
            ->addRole($projectRole)
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable());

        $me = (new User())
            ->setCreateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable())
            ->setAuth($myAuth)
            ->setPseudo('Serge Mezui')
            ->setSlug('SergeMezui')
            ->setCountry('Gabon')
            ->setCoins(1000)
            ->setBornAt(new \DateTimeImmutable('2002-10-04 17:24:43.000000'));

        $manager->persist($myAuth);
        $manager->persist($me);

        // Users
        for ($i = 0; $i < 5; $i++) {

            $auth = new UserAuthentication();
            $pass = $this->encoder->hashPassword($auth, 'pass');

            $auth
                ->setEmail($fake->email())
                ->setPassword($pass)
                ->setBlocked($fake->boolean())
                ->setCreateAt(new \DateTimeImmutable())
                ->setUpdateAt(new \DateTimeImmutable())
                ->addRole($userRole);

            $manager->persist($auth);

            $user = (new User())
                ->setCreateAt(new \DateTimeImmutable())
                ->setUpdateAt(new \DateTimeImmutable())
                ->setAuth($auth)
                ->setSlug('slugUser' . $i)
                ->setPseudo($fake->name())
                ->setCountry($fake->countryCode())
                ->setCoins(10)
                ->setBornAt(new \DateTimeImmutable());

            $manager->persist($user);
        }


        $manager->persist($appName);
        $manager->persist($appVersion);

        $manager->flush();
    }
}
