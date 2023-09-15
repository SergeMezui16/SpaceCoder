<?php

namespace App\Authentication\Form;

use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Authentication\Entity\UserAuthentication;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAuthType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email', 
                EmailType::class, 
                $this->typer->setConfiguration('Email', 'Ex. albi@minou.com', true)
            )
            ->add(
                'password', 
                PasswordType::class, 
                $this->typer->setConfiguration('Mot de passe', '', true, 'Le mot de passe doit contenir au moins huit caractères, au moins une lettre, un chiffre et un caractère spécial. [ # ? ! @ $ % ^ & * - ]')
            )
            ->add(
                'confirmPassword', 
                PasswordType::class, 
                $this->typer->setConfiguration('Confimer le mot de passe', '', true)
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAuthentication::class,
        ]);
    }
}
