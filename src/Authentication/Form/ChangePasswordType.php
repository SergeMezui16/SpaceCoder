<?php

namespace App\Authentication\Form;

use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Authentication\Form\Model\ChangePasswordModel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ChangePasswordType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class, 
                $this->typer->setConfiguration('Mot de passe actuel', '', true)
            )
            ->add(
                'newPassword',
                PasswordType::class, 
                $this->typer->setConfiguration('Nouveau mot de passe', '', true, 'Le mot de passe doit contenir au moins huit caractères, au moins une lettre, un chiffre et un caractère spécial. [ # ? ! @ $ % ^ & * - ]')
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
            'data_class' => ChangePasswordModel::class
        ]);
    }
}
