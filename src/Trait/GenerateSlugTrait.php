<?php

namespace App\Trait;


use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * This trait allows to give default values to properties of 
 * an entity before it is persisted in the database
 */
#[ORM\HasLifecycleCallbacks]
trait GenerateSlugTrait
{
    /**
     * Generate a slug from pseudo before persist or update
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlug() {
        $this->slug = (new Slugify())->slugify($this);
    }
}