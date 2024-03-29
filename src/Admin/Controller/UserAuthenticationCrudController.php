<?php

namespace App\Admin\Controller;

use App\Admin\Controller\RoleCrudController;
use App\Admin\Service\EntityListService;
use App\Authentication\Entity\Role;
use App\Authentication\Entity\UserAuthentication;
use App\Authentication\Repository\RoleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserAuthenticationCrudController extends AbstractCrudController
{

    public function __construct(private EntityListService $list)
    {
        
    }
    public static function getEntityFqcn(): string
    {
        return UserAuthentication::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?UserAuthentication $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Auth'
            )
            ->setEntityLabelInPlural('Auth')
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')->hideOnForm();
        yield ArrayField::new('ip', 'Adresses Ip')->onlyOnDetail();
        yield EmailField::new('email', 'Email');
        yield AssociationField::new('role', 'Role');
        
        yield BooleanField::new('blocked', 'Bloqué')->hideWhenCreating();
        yield DateTimeField::new('firstconnexion', 'Première Connexion')->onlyOnDetail();
        yield DateTimeField::new('lastconnexion', 'Dernière Connexion')->hideOnForm();
        yield DateTimeField::new('deleteAt', 'Suprimé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->onlyOnDetail();
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
