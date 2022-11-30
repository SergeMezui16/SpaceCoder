<?php

namespace App\Authentication\Form;

use App\Entity\User;
use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{

    public function __construct(private ConfigureTypeService $typer){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'pseudo', 
                TextType::class, 
                $this->typer->setConfiguration('Pseudo', 'Ex. Albi Minou', true, 'Entrez un pseudonyme unique')
            )
            ->add(
                'bornAt', 
                DateType::class, 
                $this->typer->setDateConfiguration('Date de naissance', true)
            )
            ->add(
                'auth', 
                UserAuthType::class
            )
            ->add(
                'country', 
                CountryType::class, 
                $this->typer->select('Pays', '--- Choisissez Votre pays ---', true)
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
