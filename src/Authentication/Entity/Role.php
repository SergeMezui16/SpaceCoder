<?php

namespace App\Authentication\Entity;

use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Repository\RoleRepository;
use App\Entity\Project;
use App\Interface\EntityLifecycleInterface;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Role implements EntityLifecycleInterface
{

    use LifecycleTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $context = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $valid;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: Project::class)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: UserAuthentication::class)]
    private Collection $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
    } 

    public function __toString()
    {
        return $this->name;
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

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return Collection<int, UserAuthentication>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserAuthentication $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(UserAuthentication $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setRole($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getRole() === $this) {
                $project->setRole(null);
            }
        }

        return $this;
    }
}
