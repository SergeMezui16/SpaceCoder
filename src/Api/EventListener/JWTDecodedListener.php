<?php
namespace App\Api\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * JWTDecodedListener
 * 
 * Check if the IP address of client is the same from token and request
 */
class JWTDecodedListener
{
    public function __construct(private RequestStack $requestStack)
    {}
    
    /**
     * On JWT Decoded
     *
     * @param JWTDecodedEvent $event
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getPayload();

        if (!isset($payload['requestIp']) || $payload['requestIp'] !== $request->getClientIp()) {
            $event->markAsInvalid();
        }

    }
}