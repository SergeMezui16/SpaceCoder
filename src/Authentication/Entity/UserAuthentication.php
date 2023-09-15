<?php

namespace App\Authentication\Entity;


use App\Authentication\Entity\Role;
use App\Authentication\Repository\UserAuthenticationRepository;
use App\Entity\Contact;
use App\Entity\User;
use App\Traits\PrePersistTrait;
use App\Traits\PreUpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserAuthenticationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    'email',
    message: 'Un utilisateur avec cet email existe déjà.'
)]
class UserAuthentication implements UserInterface, PasswordAuthenticatedUserInterface
{
    use PreUpdateTrait;
    use PrePersistTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'Cet email n\'est pas valide.',
    )]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^(?=.*?[a-zA-Z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/',
        message: 'Ce mot de passe n\'est pas valide.'
    )]
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

    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'Les mots de passe ne sont pas identiques.'
    )]
    public ?string $confirmPassword;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contact::class)]
    private Collection $contacts;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Role $role = null;


    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleteAt = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }

    /**
     * Reload connexion informations
     *
     * @param Request $request
     * @return UserAuthentication
     */
    public function load(Request $request): self
    {
        $this->addIp($request->getClientIp());
        $this->lastConnexion = new \DateTimeImmutable();
        if($this->firstConnexion === null) $this->firstConnexion = new \DateTimeImmutable();

        return $this;
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
     * @return array|Collection<int, Role>
     */
    public function getRoles(): array
    {
        $role = $this->role;

        if (empty($role)) {
            return ['ROLE_USER'];
        }

        return [$role->getName()]; 
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
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
        $this->confirmPassword = null;
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

    public function getDeleteAt(): ?\DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deleteAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }
}
