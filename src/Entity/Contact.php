<?php

namespace App\Entity;

use App\Authentication\Entity\UserAuthentication;
use App\Interface\EntityLifecycleInterface;
use App\Repository\ContactRepository;
use App\Traits\LifecycleTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Contact implements EntityLifecycleInterface
{

    use LifecycleTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $object = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    public $recaptcha;

    #[ORM\Column(options:['default' => false])]
    private ?bool $done = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    private ?UserAuthentication $user = null;


    public function __toString()
    {
        return "$this->name - $this->email";
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getUser(): ?UserAuthentication
    {
        return $this->user;
    }

    public function setUser(?UserAuthentication $user): self
    {
        $this->user = $user;

        return $this;
    }
}
