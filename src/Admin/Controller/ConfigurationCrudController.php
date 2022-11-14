<?php

namespace App\Admin\Controller;

use App\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Configuration $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Configuration'
            )
            ->setEntityLabelInPlural('Configurations')
        ;
    }

    public function configureFields(string $pageName): iterable
    {        
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('category', 'Categorie');
        yield TextareaField::new('value', 'Valeur');
        yield DateField::new('updateAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateField::new('createAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
