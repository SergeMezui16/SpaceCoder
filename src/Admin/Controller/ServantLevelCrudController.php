<?php

namespace App\Admin\Controller;

use App\Servant\Entity\ServantLevel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServantLevelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ServantLevel::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?ServantLevel $config, ?string $pageName) => $config ? $config->__toString() : 'un niveau'
            )
            ->setEntityLabelInPlural('Niveaux')
            ->setDefaultSort(['title' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
