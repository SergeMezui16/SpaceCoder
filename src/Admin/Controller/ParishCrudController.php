<?php

namespace App\Admin\Controller;

use App\Admin\Controller\ZoneCrudController;
use App\Servant\Entity\Parish;
use App\Servant\Entity\ParishStatus;
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

class ParishCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parish::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Parish $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Paroisse'
            )
            ->setEntityLabelInPlural('Paroisses')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('code', 'ID')->hideOnForm();
        yield AssociationField::new('zone', 'Zone')->hideOnIndex()->setCrudController(ZoneCrudController::class);
        yield AssociationField::new('status', 'Statut')->hideOnIndex()->setCrudController(ParishStatus::class);
        yield TextField::new('name', 'Nom');
        yield TextField::new('initial', 'Initiales')->hideOnIndex();
        yield TextField::new('location', 'Localisation');
        yield TextField::new('priest', 'Curé');
        yield TextField::new('chaplain', 'Aumônier Paroissial')->hideOnIndex();
        yield TextField::new('patronSaint', 'Saint Patron')->hideOnIndex();
        yield ImageField::new('image', 'Image')->hideOnIndex()->setUploadedFileNamePattern('parish-[contenthash].[extension]')->setUploadDir('public/data/servant/images')->setBasePath('/data/servant/images/');
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
