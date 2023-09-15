<?php

namespace App\Admin\Controller;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Comment $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Commentaire'
            )
            ->setEntityLabelInPlural('Commentaires')
            ->setDefaultSort(['createAt' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {        
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('author', 'Auteur');
        yield AssociationField::new('article', 'Article');
        yield TextareaField::new('content', 'Contenu');
        yield AssociationField::new('replyTo', 'Reponse à');
        yield AssociationField::new('replies', 'Reponses')->hideOnForm();
        yield ArrayField::new('replies', 'Reponses')->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('updateAt', 'Modifié(e) le')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('createAt', 'Créé(e) le')->hideOnForm()->hideOnIndex();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
