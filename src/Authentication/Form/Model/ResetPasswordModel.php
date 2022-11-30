<?php

namespace App\Authentication\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordModel
{
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
