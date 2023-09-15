<?php

namespace App\Form;

use App\Entity\Contact;
use App\Service\ConfigureTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;

class ContactType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer)
    {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, $this->typer->setConfiguration('Votre Nom', 'Ex. Albi Minou', true))
            ->add('email', EmailType::class, $this->typer->setConfiguration('Votre Email', 'Ex. albi@minou.com', true))
            ->add('object', TextType::class, $this->typer->setConfiguration('Objet', 'Ex. Demande de restauration de compte.', true))
            ->add('message', TextareaType::class, $this->typer->comment('Votre Message'))
            ->add('recaptcha', RecaptchaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
