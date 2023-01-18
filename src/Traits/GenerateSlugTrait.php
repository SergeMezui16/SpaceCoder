<?php

namespace App\Traits;


use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * Generate a slug
 * 
 * This trait allows to give default values to properties of 
 * an entity before it is persisted in the database
 */
#[ORM\HasLifecycleCallbacks]
trait GenerateSlugTrait
{
    /**
     * Generate a slug before persist or update
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlug() {
        $this->slug = (new Slugify())->slugify($this);
    }
}