<?php

namespace App\Authentication\Form;

use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Authentication\Entity\UserAuthentication;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ChangeEmailType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'email', 
            EmailType::class, 
            $this->typer->setConfiguration('Nouvelle adresse mail', 'Ex. albi@minou.com', true)
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
