<?php

namespace App\Admin\Controller;

use App\Admin\Controller\UserAuthenticationCrudController;
use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Notification $notif, ?string $pageName) => $notif ? '"' . $notif->__toString() . '"' : 'Notification'
            )
            ->setEntityLabelInPlural('Notifications')
            ->setDefaultSort(['sentAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield TextField::new('header', 'En tête')->hideOnIndex();
        yield TextField::new('action', 'Action')->hideOnIndex();
        yield AssociationField::new('recipients', 'Utilisateurs')->setCrudController(UserAuthenticationCrudController::class);
        yield BooleanField::new('saw', 'Vue');
        yield TextareaField::new('content', 'Contenu');
        yield DateTimeField::new('sentAt', 'Envoyé(e) le');

        yield DateTimeField::new('updateAt', 'Modifié(e) le')->hideOnIndex();
        yield DateTimeField::new('createAt', 'Créé(e) le')->hideOnIndex();
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
