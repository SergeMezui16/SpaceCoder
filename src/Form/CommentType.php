<?php

namespace App\Form;

use App\Entity\Comment;
use App\Service\ConfigureTypeService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function __construct(private ConfigureTypeService $typer){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content', 
                TextareaType::class,
                $this->typer->comment('Votre commentaire')
            )
            ->add(
                'replyTo',
                EntityType::class,
                $this->typer->entity('Répondre à', Comment::class, 'content', ['style' => 'display: none;', 'readonly' => true])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'method' => 'POST'
        ]);
    }
}
