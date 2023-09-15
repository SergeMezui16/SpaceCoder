<?php

namespace App\Event;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched when an email will be sent
 */
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
