<?php

namespace App\Admin\Controller;

use App\Admin\Controller\ServantLevelCrudController;
use App\Admin\Controller\ServantPostCrudController;
use App\Servant\Entity\Servant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Servant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Servant $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'un servant'
            )
            ->setEntityLabelInPlural('Servants')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('code', 'Matricule')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('surname', 'Prénom')->hideOnIndex();
        yield AssociationField::new('parish', 'Paroisse')->setCrudController(ParishCrudController::class);
        yield AssociationField::new('level', 'Niveau')->hideOnIndex()->setCrudController(ServantLevelCrudController::class);
        yield AssociationField::new('post', 'Poste')->hideOnIndex()->setCrudController(ServantPostCrudController::class);
        yield DateField::new('birthday', 'Date de Naissance');
        yield ChoiceField::new('sex', 'Sexe')->setChoices([
            'Feminin' => 'F',
            'Masculin' => 'M',
        ]);
        yield TextField::new('phone', 'Téléphone')->hideOnIndex();
        yield DateField::new('startAt', 'Date de Début')->hideOnIndex();
        yield ImageField::new('photo', 'Photo')->setUploadedFileNamePattern('servant-[contenthash].[extension]')->setUploadDir('public/data/servant/images')->setBasePath('/data/servant/images/');
        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating()->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
