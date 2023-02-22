<?php

namespace App\Entity;

use App\Authentication\Entity\Role;
use App\Authentication\Entity\UserAuthentication;
use App\Entity\Article;
use App\Entity\Comment;
use App\Interface\SearchableInterface;
use App\Model\SearchItemModel;
use App\Repository\UserRepository;
use App\Traits\GenerateSlugTrait;
use App\Traits\PrePersistTrait;
use App\Traits\PreUpdateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    'pseudo',
    message: 'Ce pseudo existe déjà, veuillez réessayer avec un nouveau.'
)]
class User implements SearchableInterface
{
    use PreUpdateTrait;
    use PrePersistTrait;
    use GenerateSlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Le pseudo doit contenir au moins 3 caractères.',
        maxMessage: 'Le pseudo doit contenir au plus 20 caractères.',
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: '2048k',
        maxSizeMessage: 'Cette image est trop lourde.',
        mimeTypes: ['image/jpg', 'image/png', 'image/gif', 'image/jpeg'],
        mimeTypesMessage: 'Le format de l\'image n\'est pas valide.',
    )]
    private ?string $avatar = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $coins = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $bornAt = null;

    #[Assert\Valid]
    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserAuthentication $auth = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\OneToMany(mappedBy: 'suggestedBy', targetEntity: Article::class, orphanRemoval: false)]
    private Collection $suggestions;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class, orphanRemoval: false)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: false)]
    private Collection $comments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\ManyToMany(targetEntity: Notification::class, mappedBy: 'recipients')]
    private Collection $notifications;

    public function __construct()
    {
        $this->suggestions = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->pseudo;
    }

    /**
     * Check if user has notifications not read
     *
     * @return boolean
     */
    public function hasNotification(): bool
    {
        foreach ($this->notifications as $notification) {
            if(!$notification->isSaw()) return true;
        }
        return false;
    }

    /**
     * Get the number of unsee Notifications
     *
     * @return integer
     */
    public function countUnseeNotification(): int
    {
        $n = 0;
        foreach($this->getNotifications() as $notif){
            if(!$notif->isSaw()) $n++;
        }
        return $n;
    }

    public function getSearchItem(int $id): SearchItemModel
    {
        return (new SearchItemModel())
            ->setId($id)
            ->setTitle($this->pseudo)
            ->setNature('User')
            ->setPublishedAt($this->createAt)
            ->setDescription($this->bio)
            ->setUrl($this->slug)
            ->setOther($this);
    }

    /**
     *
     * @return Collection<int, Role>
     */
    public function getAuthRole() : Role
    {
        return $this->auth->getRole();
    }

    public function setAuthRole(Role $roles) : self
    {
        $this->auth->setRole($roles);

        return $this;
    }

    public function isAuthBlocked() : bool
    {
        return $this->auth->isBlocked();
    }

    public function setAuthBlocked(bool $blocked) : self
    {
        $this->auth->setBlocked($blocked);

        return $this;
    }

    public function getAuthLastConnexion(): ?\DateTimeInterface
    {
        return $this->auth->getLastConnexion();
    }

    public function setAuthLastConnexion(?\DateTimeInterface $lastConnexion): self
    {
        $this->auth->setLastConnexion($lastConnexion);

        return $this;
    }

    public function getFirstConnexion(): ?\DateTimeInterface
    {
        return $this->auth->getFirstConnexion();
    }

    public function setFirstConnexion(?\DateTimeInterface $firstConnexion): self
    {
        $this->auth->setFirstConnexion($firstConnexion);

        return $this;
    }

    public function getAuthIp(): array
    {
        return [...$this->auth->getIP()];
    }

    public function setAuthIp(?array $ip): self
    {
        $this->auth->setIp($ip);

        return $this;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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

    public function getCoins(): ?int
    {
        return $this->coins;
    }

    public function setCoins(int $coins): self
    {
        $this->coins = $coins;

        return $this;
    }

    public function getBornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(\DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getAuth(): ?UserAuthentication
    {
        return $this->auth;
    }

    public function setAuth(?UserAuthentication $auth): self
    {
        $this->auth = $auth;

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

    /**
     * @return Collection<int, Article>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Article $suggestion): self
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions->add($suggestion);
            $suggestion->setSuggestedBy($this);
        }

        return $this;
    }

    public function removeSuggestion(Article $suggestion): self
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getSuggestedBy() === $this) {
                $suggestion->setSuggestedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->addRecipient($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeRecipient($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getLastsNotifications()
    {
        $notifications = $this->notifications->toArray();
        
        usort($notifications, fn ($a, $b) => $a->getSentAt() < $b->getSentAt());

        return $notifications;
    }
}
