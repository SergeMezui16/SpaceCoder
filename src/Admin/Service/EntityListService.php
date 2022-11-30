<?php 

namespace App\Admin\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;


class EntityListService 
{
   public function __construct(private EntityManagerInterface $manager)
   {}


    /**
     * Get entity list for load field
     *
     * @return Collection
     */
    public function getEntityList(string $entityClass): Collection
    {
        // $collection =  new \Countable();
        foreach($this->manager->getRepository($entityClass)->findAll() as $entity){

            $entities[$entity->__toString()] = $entity;
        }
        // return $this->manager->getRepository($entityClass)->findAll();
        // dd($collection);
        return $entities;
    }
}