<?php

namespace App\Authentication\Form;

use App\Entity\User;
use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'avatar',
                FileType::class,
                $this->typer->avatar('Avatar', '', false, 'Cliquer sur l\'image pour la changer. L’image doit être aux formats d’image (jpeg, jpg, gif, png) et pas peser plus de 2 Mo.')
            )
            ->add(
                'country', 
                CountryType::class, 
                $this->typer->select('Pays', '--- Choisissez Votre pays ---', false)
            )
            ->add(
                'bornAt', 
                DateType::class, 
                $this->typer->setDateConfiguration('Date de naissance', required: false)
            )
            ->add(
                'bio',
                TextareaType::class,
                $this->typer->setConfiguration('Bio', options:['maxlength' => 255])
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
