<?php

namespace App\Admin\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
        yield EmailField::new('email', 'Email');
        yield CountryField::new('country', 'Pays');

        yield CollectionField::new('authRole', 'Roles')->onlyOnIndex();
        yield ArrayField::new('authRole', 'Roles')->hideOnIndex();

        yield ImageField::new('avatar', 'avatar')->onlyOnDetail();
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('title')->onlyOnDetail();
        yield NumberField::new('coins', 'Points');
        yield ArrayField::new('authip', 'Adresses Ip')->onlyOnDetail();

        yield ArrayField::new('comments', 'Commentaires')->onlyOnDetail();

        yield ArrayField::new('articles', 'Articles')->onlyOnDetail();
        yield ArrayField::new('suggestions', 'Suggestions')->onlyOnDetail();
        yield BooleanField::new('authblocked', 'Bloqué')->hideWhenCreating()->renderAsSwitch(false);
        yield DateTimeField::new('authlastconnexion', 'Dernière Connexion');
        yield DateTimeField::new('authfirstconnexion', 'Première Connexion')->onlyOnDetail();
        yield DateTimeField::new('bornAt', 'Né(e) le')->onlyOnDetail();
        yield DateTimeField::new('updateAt', 'Modifié(e) le')->onlyOnDetail();
        yield DateTimeField::new('createAt', 'Créé(e) le')->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }
}
