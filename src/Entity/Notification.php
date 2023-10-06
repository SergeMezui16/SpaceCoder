<?php

namespace App\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\NotificationRepository;
use App\Traits\LifecycleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification implements EntityLifecycleInterface
{
    use LifecycleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'notifications')]
    private Collection $recipients;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $header = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $views = [];

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }


    /**
     * Generate the array of views from recipients and set it
     * 
     * This function must be called after 'setRecipients()' function
     * This is called after each update and persist of the object
     *
     * @return self
     */
    #[ORM\PostUpdate]
    #[ORM\PrePersist]
    public function initViews(): self
    {
        $views = [...$this->views];

        foreach ($this->recipients as $recipient) {
            if (!array_key_exists($recipient->getPseudo(), $views)) {
                $views[$recipient->getPseudo()] = false;
            }
        }

        $this->setViews($views);

        return $this;
    }

    /**
     * Check if this user saw or not this Notification
     *
     * @param User $user
     * @return boolean
     */
    public function hasBeenViewedBy(User $user): bool
    {
        return array_key_exists($user->getPseudo(), $this->views) && $this->views[$user->getPseudo()] === true;
    }

    /**
     * Set Notification as viewed for this user
     *
     * @param User $user
     * @return void
     */
    public function markAsViewedFor(User $user)
    {
        $views = [...$this->views];

        if ($user->getNotifications()->contains($this)) {
            $views[$user->getPseudo()] = true;
        }

        $this->setViews($views);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(User $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }

        return $this;
    }

    public function removeRecipient(User $recipient): self
    {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getViews(): array
    {
        return $this->views;
    }

    public function setViews(?array $views): self
    {
        $this->views = $views;

        return $this;
    }
}
