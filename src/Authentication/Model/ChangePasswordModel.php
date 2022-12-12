<?php
namespace App\Authentication\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordModel 
{
    #[SecurityAssert\UserPassword()]
    private string $oldPassword;

    #[Assert\Regex(
        pattern: '/^(?=.*?[a-zA-Z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/',
        message: 'Ce mot de passe n\'est pas valide.'
    )]
    private string $newPassword;

    #[Assert\EqualTo(
        propertyPath: 'newPassword',
        message: 'Les mots de passe ne sont pas identiques.'
    )]
    private string $confirmPassword;



    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }
}