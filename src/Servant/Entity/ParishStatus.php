<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\ParishStatusRepository;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: ParishStatusRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_parish_status')
]
#[Broadcast]
class ParishStatus implements EntityLifecycleInterface
{

    use LifecycleTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Parish::class)]
    private Collection $parishes;

    public function __construct()
    {
        $this->parishes = new ArrayCollection();
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
     * @return Collection<int, Parish>
     */
    public function getParishes(): Collection
    {
        return $this->parishes;
    }

    public function addParish(Parish $parish): static
    {
        if (!$this->parishes->contains($parish)) {
            $this->parishes->add($parish);
            $parish->setStatus($this);
        }

        return $this;
    }

    public function removeParish(Parish $parish): static
    {
        if ($this->parishes->removeElement($parish)) {
            // set the owning side to null (unless already changed)
            if ($parish->getStatus() === $this) {
                $parish->setStatus(null);
            }
        }

        return $this;
    }
}
