<?php

namespace App\Interface;


use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * EntityLifecycleInterface
 * 
 * Define the update an create date for the entity lifecycle from the ORM
 */
#[HasLifecycleCallbacks]
interface EntityLifecycleInterface
{
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self;
    public function setCreatedAt(\DateTimeImmutable $createdAt): self;
}
