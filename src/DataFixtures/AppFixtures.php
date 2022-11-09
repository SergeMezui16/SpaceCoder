<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $appName = (new Configuration())
                    ->setName('APP_NAME')
                    ->setValue('SPACECODER')
                    ->setCategory('APP')
                    ->setCreateAt(new \DateTimeImmutable())
                    ->setUpdateAt(new \DateTimeImmutable())
            ;
        $appVersion = (new Configuration())
                    ->setName('APP_VERSION')
                    ->setValue('3.0')
                    ->setCategory('APP')
                    ->setCreateAt(new \DateTimeImmutable())
                    ->setUpdateAt(new \DateTimeImmutable())
            ;
        
        $manager->persist($appName);
        $manager->persist($appVersion);

        $manager->flush();
    }
}
