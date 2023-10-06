<?php

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Set a default createdAt and UpdatedAt
 * 
 * This trait allows to give default values to properties of 
 * an entity before it is persisted in the database
 */
trait PrePersistTrait
{
    #[ORM\Column]
    #[Groups('Lifecycle')]
    private ?\DateTimeImmutable $createdAt = null;
    /**
     * Init default date on creation
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
