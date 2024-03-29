<?php

namespace App\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Interface\SearchableInterface;
use App\Model\SearchItemModel;
use App\Repository\RessourceRepository;
use App\Traits\GenerateSlugTrait;
use App\Traits\LifecycleTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Ressource implements SearchableInterface, EntityLifecycleInterface
{
    use LifecycleTrait;
    use GenerateSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $clicks = 0;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column]
    private array $categories = [];

    public function __toString()
    {
        return $this->name;
    }

    public function getSearchItem(int $id): SearchItemModel
    {
        return (new SearchItemModel())
            ->setId($id)
            ->setTitle($this->name)
            ->setNature('Ressource')
            ->setPublishedAt($this->createdAt)
            ->setDescription($this->description)
            ->setUrl($this->slug);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    public function setClicks(int $clicks): self
    {
        $this->clicks = $clicks;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
