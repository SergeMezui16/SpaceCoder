<?php
namespace App\Api\EventListener;

use App\Authentication\Entity\UserAuthentication;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;

/**
 * JWT Created Listener
 * 
 * Enhance token payload with user data
 * 
 * If user has been blocked, an Exception is thrown
 */
class JWTCreatedListener 
{

    public function __construct(
        private RequestStack $requestStack,
        private UrlHelper $urlHelper
    )
    {}

    /**
     * On JWT Created
     *
     * @param JWTCreatedEvent $event
     * @throws MissingTokenException if user is blocked
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var UserAuthentication $auth */
        $auth = $event->getUser();


        if($auth->isBlocked()){
            throw new MissingTokenException('User Blocked.', 401);
        }

        $user = $auth->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getData();

        $payload['pseudo'] = $user->getPseudo();
        $payload['requestIp'] = $request->getClientIp();

        $event->setData($payload);
    }

}