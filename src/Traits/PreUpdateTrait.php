<?php

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;

/**
 * This trait allows to give default values to properties of 
 * an entity before his data has udated on database
 */
#[ORM\HasLifecycleCallbacks]
trait PreUpdateTrait
{
    /**
     * Update date before update of Entity
     * @return void
     */
    #[ORM\PreUpdate]
    public function preUpdate() : void
    {
        $this->setUpdateAt(new \DateTimeImmutable());
    }
}