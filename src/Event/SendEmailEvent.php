<?php

namespace App\Event;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\EventDispatcher\Event;

class SendEmailEvent extends Event
{
    public function __construct(
        protected TemplatedEmail $email
    ) {
    }

    public function getEmail(): TemplatedEmail
    {
        return $this->email;
    }
}
