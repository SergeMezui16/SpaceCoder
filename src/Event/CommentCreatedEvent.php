<?php
namespace App\Event;

use App\Authentication\Entity\UserAuthentication;
use App\Entity\Comment;
use Symfony\Contracts\EventDispatcher\Event;

class CommentCreatedEvent extends Event
{
    public function __construct(
        private Comment $comment,
        private UserAuthentication $auth
    )
    {}

    /**
     * Get the value of comment
     */ 
    public function getComment(): Comment
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @param Comment $comment
     * @return  self
     */ 
    public function setComment(Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the value of auth
     */ 
    public function getAuth(): UserAuthentication
    {
        return $this->auth;
    }

    /**
     * Set the value of auth
     *
     * @param UserAuthentication $auth
     * @return  self
     */ 
    public function setAuth(UserAuthentication $auth): static
    {
        $this->auth = $auth;

        return $this;
    }
}