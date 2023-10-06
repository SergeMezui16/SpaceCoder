<?php

namespace App\Servant\Entity;

use App\Interface\EntityLifecycleInterface;
use App\Repository\ServantRepository;
use App\Servant\Entity\Parish;
use App\Traits\LifecycleTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[
    ORM\Entity(repositoryClass: ServantRepository::class),
    ORM\HasLifecycleCallbacks,
    ORM\Table('servant_servant')
]
#[Broadcast]
class Servant implements EntityLifecycleInterface
{

    use LifecycleTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $surname = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column( length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $sex = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'Novice'])]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'servants')]
    #[Assert\NotBlank, Assert\Valid]
    private ?Parish $parish = null;

    #[ORM\ManyToOne(inversedBy: 'servants')]
    #[Assert\NotBlank, Assert\Valid]
    private ?ServantPost $post = null;

    #[ORM\ManyToOne(inversedBy: 'servants')]
    #[Assert\NotBlank, Assert\Valid]
    private ?ServantLevel $level = null;

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
        return strtoupper($this->parish?->getInitial()) . $this->startAt->format('o') . ($this->id < 10 ? "0" : "") . $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeImmutable $bithday): static
    {
        $this->birthday = $bithday;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getParish(): ?Parish
    {
        return $this->parish;
    }

    public function setParish(?Parish $parish): static
    {
        $this->parish = $parish;

        return $this;
    }

    public function getPost(): ?ServantPost
    {
        return $this->post;
    }

    public function setPost(?ServantPost $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getLevel(): ?ServantLevel
    {
        return $this->level;
    }

    public function setLevel(?ServantLevel $level): static
    {
        $this->level = $level;

        return $this;
    }
}
