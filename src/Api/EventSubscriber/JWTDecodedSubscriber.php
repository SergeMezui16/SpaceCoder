<?php

namespace App\Api\EventSubscriber;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * JWTDecodedListener
 * 
 * Check if the IP address of client is the same from token and request
 */
class JWTDecodedSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {}

    /**
     * On JWT Decoded
     *
     * @param JWTDecodedEvent $event
     * @return void
     */
    public function onLexikJwtAuthenticationOnJwtDecoded(JWTDecodedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getPayload();

        if (!isset($payload['requestIp']) || $payload['requestIp'] !== $request->getClientIp()) {
            $event->markAsInvalid();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_decoded' => 'onLexikJwtAuthenticationOnJwtDecoded',
        ];
    }
}
