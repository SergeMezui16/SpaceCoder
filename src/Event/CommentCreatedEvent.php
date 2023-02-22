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
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the value of auth
     */ 
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set the value of auth
     *
     * @return  self
     */ 
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }
}