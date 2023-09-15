<?php

namespace App\Authentication\Form;

use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteUserAccountType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'password',
                PasswordType::class,
                $this->typer->userPassword('Entrez votre mot de passe', '', true)
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
