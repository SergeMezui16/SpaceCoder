<?php

namespace App\Authentication\Entity;


use App\Trait\PreUpdateTrait;
use App\Trait\PrePersistTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Authentication\Repository\UserAuthenticationRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserAuthenticationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserAuthentication implements UserInterface, PasswordAuthenticatedUserInterface
{
    use PreUpdateTrait;
    use PrePersistTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'users')]
    private ?Collection $roles;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $blocked = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastConnexion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $firstConnexion = null;

    #[ORM\Column(nullable: true)]
    private array $ip = [];


    #[ORM\OneToOne(mappedBy: 'auth', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }


    /**
     * Verify if is the first connexion
     *
     * @return boolean
     */
    public function isFirstConnexion() : bool
    {
        return $this->firstConnexion === null ? true : false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return Collection<int, Role>
     */
    public function getRoles(): array
    {
        $roles = $this->roles->map(function($role){
            return $role->getName();
        })->toArray();
        
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(Collection $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(Role $newRole): self
    {
        if (!$this->roles->contains($newRole)) {
            $this->roles[] = $newRole;
            $newRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $newRole): self
    {
        if ($this->roles->removeElement($newRole)) {
            $newRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTimeInterface $lastConnexion): self
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }

    public function getFirstConnexion(): ?\DateTimeInterface
    {
        return $this->firstConnexion;
    }

    public function setFirstConnexion(?\DateTimeInterface $firstConnexion): self
    {
        $this->firstConnexion = $firstConnexion;

        return $this;
    }

    public function getIp(): array
    {
        return $this->ip;
    }

    public function setIp(?array $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function addIp(?string $ip) : self
    {
        if(!in_array($ip, $this->ip)) $this->ip[] = $ip;
        return $this;
    }

    public function removeIp(?string $ip)
    {
        $this->ip = array_filter(
            array_map(function($i) use ($ip) {
                if($i !== $ip) return $i;
                return;
            }, $this->ip)
        );
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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setAuth(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getAuth() !== $this) {
            $user->setAuth($this);
        }

        $this->user = $user;

        return $this;
    }
}
