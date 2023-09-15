<?php
namespace App\Service;

use App\Event\SendEmailEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Address;

/**
 * Mail Maker
 * 
 * This service allow us to make a Template of email and send it
 * It make a TemplatedEmail and dispatch a SendEmailEvent
 * Email are sent from the default email defined in mailer.yaml
 * 
 * @author SergeMezui <contact@sergemezui.com>
 */
class MailMakerService extends TemplatedEmail
{

    public function __construct(
        private EventDispatcherInterface $dispacher
    )
    {
        parent::__construct();
    }

    /**
     * Make a TemplatedEmail
     *
     * @param string|array $recitents Adresse of recipent(s)
     * @param string $subject Subject of the mail
     * @param string $templatePath Path of the Twig template
     * @param array $context Variables needed by Twig template
     * @param string $from Add an adresse from
     * @return self
     */
    public function make($recitents, string $subject, string $templatePath, array $context, string $from = ''): self
    {

        if( \is_array($recitents)){
            foreach ($recitents as $recitent) $this->addTo(new Address($recitent));
        } else {
            $this->to(new Address($recitents));
        }

        $this
            ->subject($subject)
            ->htmlTemplate($templatePath)
            ->context([
                ...$context,
                'subject' => $subject
            ]);

        if ($from !== '') $this->addFrom(new Address($from));

        return $this;
    }

    /**
     * Dispatch a SendEmailEvent
     *
     * @return void
     */
    public function send(): void
    {
        $this->dispacher->dispatch(new SendEmailEvent($this));
        return;
    }
}