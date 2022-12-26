<?php
namespace App\Authentication\Security;

use App\Authentication\Entity\UserAuthentication;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof UserAuthentication) {
            return;
        }
        
        if ($user->isBlocked()) {
            throw new CustomUserMessageAccountStatusException('L\'acces à votre compte a été restreint.', code: 403);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof UserAuthentication) {
            return;
        }
        // Demande de suppression désangagé
        // user account is expired, the user may be notified
        // if ($user->isExpired()) {
        //     throw new AccountExpiredException('...');
        // }
    }
}