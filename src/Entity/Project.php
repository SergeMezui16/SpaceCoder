<?php

namespace App\Entity;

use App\Authentication\Entity\Role;
use App\Interface\SearchableInterface;
use App\Model\SearchItemModel;
use App\Repository\ProjectRepository;
use App\Traits\GenerateSlugTrait;
use App\Traits\PrePersistTrait;
use App\Traits\PreUpdateTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project implements SearchableInterface
{
    use PrePersistTrait;
    use PreUpdateTrait;
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
    private ?string $description = null;

    #[ORM\Column]
    private ?int $visit = 0;

    #[ORM\Column(length: 255)]
    private ?string $image = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255)]
    private ?string $authors = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Role $role = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;


    public function __toString()
    {
        return $this->name;
    }

    public function getSearchItem(int $id): SearchItemModel
    {
        return (new SearchItemModel())
            ->setId($id)
            ->setTitle($this->name)
            ->setNature('Project')
            ->setPublishedAt($this->createAt)
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getVisit(): ?int
    {
        return $this->visit;
    }

    public function setVisit(int $visit): self
    {
        $this->visit = $visit;

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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getAuthors(): ?string
    {
        return $this->authors;
    }

    public function setAuthors(string $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
