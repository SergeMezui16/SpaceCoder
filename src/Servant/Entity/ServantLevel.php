<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\ServantLevelRepository;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: ServantLevelRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_servant_level')
]
#[Broadcast]
class ServantLevel implements EntityLifecycleInterface
{

    use LifecycleTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'level', targetEntity: Servant::class)]
    private Collection $servants;

    public function __construct()
    {
        $this->servants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Servant>
     */
    public function getServants(): Collection
    {
        return $this->servants;
    }

    public function addServant(Servant $servant): static
    {
        if (!$this->servants->contains($servant)) {
            $this->servants->add($servant);
            $servant->setLevel($this);
        }

        return $this;
    }

    public function removeServant(Servant $servant): static
    {
        if ($this->servants->removeElement($servant)) {
            // set the owning side to null (unless already changed)
            if ($servant->getLevel() === $this) {
                $servant->setLevel(null);
            }
        }

        return $this;
    }
}
