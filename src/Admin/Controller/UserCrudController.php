<?php

namespace App\Admin\Controller;

use App\Admin\Controller\UserAuthenticationCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?User $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Utilisateur'
            )
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['createAt' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {        
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('pseudo', 'Pseudo');
        yield AssociationField::new('auth', 'Auth')->setCrudController(UserAuthenticationCrudController::class);
        yield CountryField::new('country', 'Pays');

        yield ImageField::new('avatar', 'avatar')->setUploadDir('public/data/user/avatar/')->setUploadedFileNamePattern('[slug].spacecoder.[extension]')->hideOnIndex();
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('pseudo')->hideOnIndex();
        yield TextareaField::new('bio', 'Bio')->hideOnIndex();
        yield NumberField::new('coins', 'Points');

        yield ArrayField::new('comments', 'Commentaires')->onlyOnDetail();

        yield ArrayField::new('articles', 'Articles')->onlyOnDetail();
        yield ArrayField::new('suggestions', 'Suggestions')->onlyOnDetail();
        
        yield DateTimeField::new('bornAt', 'Né(e) le')->renderAsNativeWidget(true)->hideOnIndex();
        
        yield DateTimeField::new('updateAt', 'Modifié(e) le')->onlyOnDetail();
        yield DateTimeField::new('createAt', 'Créé(e) le')->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->remove(Crud::PAGE_INDEX, Action::NEW)
                ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }
}
