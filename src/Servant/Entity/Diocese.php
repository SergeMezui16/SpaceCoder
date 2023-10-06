<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\DioceseRepository;
use App\Servant\Entity\Zone;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: DioceseRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_diocese')
]
#[Broadcast(template: 'broadcast/Diocese.stream.html.twig')]
class Diocese implements EntityLifecycleInterface
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

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'VACANT'])]
    #[Assert\NotBlank]
    private ?string $bishop = 'VACANT';

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'VACANT'])]
    #[Assert\NotBlank]
    private ?string $chaplain = 'VACANT';

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'Saint Tarcisius de Rome'])]
    #[Assert\NotBlank]
    private ?string $patronSaint = 'Saint Tarcisius de Rome';

    #[ORM\OneToMany(mappedBy: 'diocese', targetEntity: Zone::class)]
    private Collection $zones;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->zones = new ArrayCollection();
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
        return "DSE" . ($this->id < 10 ? "0" : "") . $this->id;
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

    public function getBishop(): ?string
    {
        return $this->bishop;
    }

    public function setBishop(?string $bishop): static
    {
        $this->bishop = $bishop;

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

    /**
     * @return Collection<int, Zone>
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function addZone(Zone $zone): static
    {
        if (!$this->zones->contains($zone)) {
            $this->zones->add($zone);
            $zone->setDiocese($this);
        }

        return $this;
    }

    public function removeZone(Zone $zone): static
    {
        if ($this->zones->removeElement($zone)) {
            // set the owning side to null (unless already changed)
            if ($zone->getDiocese() === $this) {
                $zone->setDiocese(null);
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
