<?php

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Set a default UpdatedAt
 * 
 * This trait allows to give default values to properties of 
 * an entity before his data has udated on database
 */
trait PreUpdateTrait
{

    #[ORM\Column]
    #[Groups('Lifecycle')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Update date before update of Entity
     * @return void
     */
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
