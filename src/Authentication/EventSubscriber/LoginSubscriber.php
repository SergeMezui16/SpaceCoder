<?php

namespace App\Authentication\EventSubscriber;

use App\Authentication\Entity\UserAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private Session $session;

    public function __construct(
        private EntityManagerInterface $em
    ){
        $this->session = new Session(new PhpBridgeSessionStorage());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
            LogoutEvent::class => 'onLogoutEvent'
        ];
    }

    /**
     * Check if User Account has been deleted and cancel the deleting process
     *
     * @param LoginSuccessEvent $event
     * @return void
     */
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $auth = $event->getUser();

        if ($auth instanceof UserAuthentication) {

            if ($auth->getDeleteAt() !== null) {
                $auth->setDeleteAt(null);
                $this->session->getFlashBag()->add('success', sprintf('Ravi de vous revoir parmi nous %s, vous avez évité que votre compte ne soit définitivement supprimé de très peu !', $auth->getUser()));
            }

            $auth->load($event->getRequest());

            $this->session->getFlashBag()->add('info', sprintf('Bienvenu %s, vous nous avez manqué !', $auth->getUser()));

            $this->em->persist($auth);
            $this->em->flush();
        }
    }

    /**
     * Add a flash message
     *
     * @param LogoutEvent $event
     * @return void
     */
    public function onLogoutEvent(LogoutEvent $event): void
    {

        $token = $event->getToken();

        if($token){
            /** @var UserAuthentication $auth */
            $auth = $token->getUser();

            $this->session->getFlashBag()->add('info', sprintf('Aurevoir %s, on espère vous revoir bientôt !', $auth->getUser()));
        }
    }
}
