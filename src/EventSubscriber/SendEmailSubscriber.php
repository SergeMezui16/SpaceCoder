<?php

namespace App\EventSubscriber;

use App\Event\SendEmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SendEmailEvent::class => 'onSendEmailEvent',
        ];
    }

    /**
     * Send an Email get by Event
     *
     * @param SendEmailEvent $event
     * @return void
     */
    public function onSendEmailEvent(SendEmailEvent $event): void
    {
        $this->mailer->send($event->getEmail());
    }
}
