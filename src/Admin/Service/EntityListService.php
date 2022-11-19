<?php 

namespace App\Admin\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class EntityListService 
{
   public function __construct(private EntityManagerInterface $manager)
   {}


    /**
     * Get user list for load field
     *
     * @return User[]
     */
    public function getUserList() {
        foreach($this->manager->getRepository(User::class)->findAll() as $user){
            $users[$user->__toString()] = $user;
        }
        return $users;
    }
}