<?php

namespace App\Entity;

use App\Entity\Article;
use App\Traits\PreUpdateTrait;
use App\Traits\PrePersistTrait;
use App\Traits\GenerateSlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Authentication\Entity\UserAuthentication;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    'pseudo',
    message: 'Ce pseudo existe déjà, veuillez réessayer avec un nouveau.'
)]
class User
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

    #[ORM\OneToMany(mappedBy: 'suggestedBy', targetEntity: Article::class)]
    private Collection $suggestions;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->suggestions = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->pseudo;
    }

    /**
     * Undocumented function
     *
     * @return Collection<int, Role>
     */
    public function getAuthRole() : array
    {
        return [...$this->auth->getRoles()];
    }

    public function setAuthRole(Collection $roles) : self
    {
        $this->auth->setRoles($roles);

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
}
