<?php

namespace App\Api\EventSubscriber;

use App\Authentication\Entity\UserAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * JWT Created Subscriber
 * 
 * Enhance token payload with user data
 * 
 * If user has been blocked, an Exception is thrown
 */
class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}


    /**
     * On JWT Created
     *
     * @param JWTCreatedEvent $event
     * @throws MissingTokenException if user is blocked
     * @return void
     */
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        /** @var UserAuthentication $auth */
        $auth = $event->getUser();

        if ($auth->isBlocked()) {
            throw new MissingTokenException('User Blocked.', 401);
        }

        $user = $auth->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getData();

        $payload['pseudo'] = $user->getPseudo();
        $payload['requestIp'] = $request->getClientIp();

        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
