<?php

namespace App\Admin\Controller;

use App\Admin\Controller\UserAuthenticationCrudController;
use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Contact $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Contact'
            )
            ->setEntityLabelInPlural('Contacts')
            ->setDefaultSort(['createAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom');
        yield TextField::new('object', 'Objet');
        yield EmailField::new('email', 'Email');
        yield TextareaField::new('message', 'Message');
        yield BooleanField::new('done', 'Traité');
        yield AssociationField::new('user', 'Utilisateur')->hideOnForm()->setCrudController(UserAuthenticationCrudController::class);

        yield DateTimeField::new('updateAt', 'Modifié(e) le')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('createAt', 'Créé(e) le')->hideOnForm();
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
