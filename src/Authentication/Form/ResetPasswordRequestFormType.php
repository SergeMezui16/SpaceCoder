<?php

namespace App\Authentication\Form;

use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{

    public function __construct(private ConfigureTypeService $typer){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email', 
                EmailType::class, 
                $this->typer->resetPasswordEmail('Email', 'Ex. albu@minou.com', 'Entez votre adresse mail pour recevoir l\'email de restauration de mot de passe.')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
