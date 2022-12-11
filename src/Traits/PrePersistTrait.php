<?php

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;

/**
 * This trait allows to give default values to properties of 
 * an entity before it is persisted in the database
 */
#[ORM\HasLifecycleCallbacks]
trait PrePersistTrait
{
    /**
     * Init default date on creation
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist() : void
    {
        $this->setCreateAt(new \DateTimeImmutable());
        $this->setUpdateAt(new \DateTimeImmutable());
    }
}