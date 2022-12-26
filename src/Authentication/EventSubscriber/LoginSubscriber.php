<?php

namespace App\Authentication\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private EntityManagerInterface $em
    ){}



    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
    
    public function onLoginSuccessEvent($event): void
    {
        /**
         * @var UserAuthentication
         */
        $user = $event->getUser();

        if ($user) {
            $user->load($event->getRequest());
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
