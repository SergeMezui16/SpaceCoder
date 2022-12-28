<?php

namespace App\Admin\Controller;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Project $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Projet'
            )
            ->setEntityLabelInPlural('Projets')
            ->setDefaultSort(['visit' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {        
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('authors', 'Auteurs');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('name')->hideOnIndex();
        yield TextField::new('description', 'Description')->hideOnIndex();
        yield NumberField::new('visit', 'Visite')->hideWhenCreating();
        yield ImageField::new('image', 'Image')->setBasePath('data/project/images/')->setUploadDir('public/data/project/images/')->setUploadedFileNamePattern('[slug].[extension]')->hideOnIndex();
        yield AssociationField::new('role', 'Role');

        yield DateTimeField::new('updateAt', 'Modifié(e) le')->hideOnForm();
        yield DateTimeField::new('createAt', 'Créé(e) le')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
