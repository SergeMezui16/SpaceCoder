<?php

namespace App\Authentication\EventSubscriber;

use App\Authentication\Entity\UserAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private Session $session;

    public function __construct(
        private EntityManagerInterface $em
    ){
        $this->session = new Session();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
            LogoutEvent::class => 'onLogoutEvent'
        ];
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        /**
         * @var UserAuthentication $user
         */
        $user = $event->getUser();

        if ($user) {

            if ($user->getDeletedAt() !== null) {
                $user->setDeletedAt(null);
                $this->session->getFlashBag()->add('success', sprintf('Ravi de vous revoir parmi nous %s, vous avez évité que votre compte ne soit définitivement supprimé de très peu !', $user->getUser()));
            }

            $user->load($event->getRequest());
            
            $this->session->getFlashBag()->add('info', sprintf('Bienvenu %s, vous nous avez manqué !', $user->getUser()));

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public function onLogoutEvent(LogoutEvent $event): void
    {}
}
