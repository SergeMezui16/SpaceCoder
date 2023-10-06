<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\ParishRepository;
use App\Servant\Entity\Zone;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: ParishRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_parish')
]
#[Broadcast]
class Parish implements EntityLifecycleInterface
{

    use LifecycleTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'parishes')]
    #[Assert\NotBlank, Assert\Valid]
    private ?ParishStatus $status = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $initial = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $priest = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'VACANT'])]
    #[Assert\NotBlank]
    private ?string $chaplain = 'VACANT';

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'Saint Tarcisius de Rome'])]
    #[Assert\NotBlank]
    private ?string $patronSaint = 'Saint Tarcisius de Rome';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'parishes')]
    #[Assert\NotBlank, Assert\Valid]
    private ?Zone $zone = null;

    #[ORM\OneToMany(mappedBy: 'parish', targetEntity: Servant::class)]
    private Collection $servants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->servants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->zone->getCode() . "PA" . ($this->id < 10 ? "0" : "") . $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?ParishStatus
    {
        return $this->status;
    }

    public function setStatus(?ParishStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInitial(): ?string
    {
        return $this->initial;
    }

    public function setInitial(string $initial): static
    {
        $this->initial = $initial;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getPriest(): ?string
    {
        return $this->priest;
    }

    public function setPriest(?string $priest): static
    {
        $this->priest = $priest;

        return $this;
    }

    public function getChaplain(): ?string
    {
        return $this->chaplain;
    }

    public function setChaplain(?string $chaplain): static
    {
        $this->chaplain = $chaplain;

        return $this;
    }

    public function getPatronSaint(): ?string
    {
        return $this->patronSaint;
    }

    public function setPatronSaint(?string $patronSaint): static
    {
        $this->patronSaint = $patronSaint;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;

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
            $servant->setParish($this);
        }

        return $this;
    }

    public function removeServant(Servant $servant): static
    {
        if ($this->servants->removeElement($servant)) {
            // set the owning side to null (unless already changed)
            if ($servant->getParish() === $this) {
                $servant->setParish(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
