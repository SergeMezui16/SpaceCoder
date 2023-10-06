<?php

namespace App\Admin\Controller;

use App\Servant\Entity\Diocese;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DioceseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Diocese::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Diocese $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Diocèse'
            )
            ->setEntityLabelInPlural('Diocèses')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('code', 'ID')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('location', 'Localisation');
        yield TextField::new('bishop', 'Éveque');
        yield TextField::new('chaplain', 'Aumonier Diocesain');
        yield TextField::new('patronSaint', 'Saint Patron')->hideOnIndex();
        yield ImageField::new('image', 'Image')->hideOnIndex()->setUploadedFileNamePattern('diocese-[contenthash].[extension]')->setUploadDir('public/data/servant/images')->setBasePath('/data/servant/images/');
        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating()->onlyOnDetail();
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
