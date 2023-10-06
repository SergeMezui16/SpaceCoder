<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\ZoneRepository;
use App\Servant\Entity\Diocese;
use App\Servant\Entity\Parish;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: ZoneRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_zone')
]
#[Broadcast]
class Zone implements EntityLifecycleInterface
{

    use LifecycleTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable:true, options: ['default' => 'VACANT'])]
    #[Assert\NotBlank]
    private ?string $vicar = 'VACANT';

    #[ORM\Column(length: 255, nullable:true, options: ['default' => 'VACANT'])]
    #[Assert\NotBlank]
    private ?string $chaplain = 'VACANT';

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'Saint Tarcisius de Rome'])]
    #[Assert\NotBlank]
    private ?string $patronSaint = 'Saint Tarcisius de Rome';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'zones')]
    #[Assert\NotBlank, Assert\Valid]
    private ?Diocese $diocese = null;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Parish::class)]
    private Collection $parishes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->parishes = new ArrayCollection();
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
        return $this->diocese->getCode() . "ZO" . ($this->id < 10 ? "0" : "") . $this->id;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getVicar(): ?string
    {
        return $this->vicar;
    }

    public function setVicar(?string $vicar): static
    {
        $this->vicar = $vicar;

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

    public function getDiocese(): ?Diocese
    {
        return $this->diocese;
    }

    public function setDiocese(?Diocese $diocese): static
    {
        $this->diocese = $diocese;

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
            $parish->setZone($this);
        }

        return $this;
    }

    public function removeParish(Parish $parish): static
    {
        if ($this->parishes->removeElement($parish)) {
            // set the owning side to null (unless already changed)
            if ($parish->getZone() === $this) {
                $parish->setZone(null);
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
