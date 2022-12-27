<?php
namespace App\Service;

use App\Event\SendEmailEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Address;


class MailMakerService extends TemplatedEmail
{
    protected TemplatedEmail $mail;

    public function __construct(
        private EventDispatcherInterface $dispacher
    )
    {
        parent::__construct();
    }


    public function make(string $to, string $subject, string $template, array $context, $from = null): self
    {
        $this
            ->to(new Address($to))
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                ...$context,
                'subject' => $subject
            ]);

        if ($from) $this->addFrom(new Address($from));

        return $this;
    }

    public function send(): void
    {
        $this->dispacher->dispatch(new SendEmailEvent($this));
        return;
    }
}