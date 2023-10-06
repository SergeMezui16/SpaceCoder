<?php

namespace App\Admin\Controller;

use App\Admin\Controller\DioceseCrudController;
use App\Servant\Entity\Zone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ZoneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Zone::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Zone $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Zone'
            )
            ->setEntityLabelInPlural('Zones')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('code', 'ID')->hideOnForm();
        yield AssociationField::new('diocese', 'Diocèse')->hideOnIndex()->setCrudController(DioceseCrudController::class);
        yield TextField::new('name', 'Nom');
        yield TextField::new('location', 'Localisation');
        yield TextField::new('vicar', 'Vicaire Épiscopal');
        yield TextField::new('chaplain', 'Aumônier Zonal')->hideOnIndex();
        yield TextField::new('patronSaint', 'Saint Patron')->hideOnIndex();
        yield TextField::new('description', 'Description')->hideOnIndex();
        yield ImageField::new('image', 'Image')->hideOnIndex()->setUploadedFileNamePattern('zone-[contenthash].[extension]')->setUploadDir('public/data/servant/images')->setBasePath('/data/servant/images/');
        yield TextareaField::new('description', 'Description')->hideOnIndex()->setNumOfRows(5);
        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating()->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
