<?php

namespace App\Admin\Controller;

use App\Entity\Ressource;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RessourceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ressource::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Ressource $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Ressource'
            )
            ->setEntityLabelInPlural('Ressources')
            ->setDefaultSort(['clicks' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {        
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('name')->hideOnIndex();
        yield TextField::new('description', 'Description')->hideOnIndex();
        yield NumberField::new('clicks', 'Visite')->hideWhenCreating();
        yield ImageField::new('image', 'Image')->setBasePath('data/project/images/')->setUploadDir('public/data/project/images/')->setUploadedFileNamePattern('[slug].[extension]')->hideOnIndex();
        yield UrlField::new('link', 'Lien');
        yield ArrayField::new('categories', 'Catégories');

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
