<?php

namespace App\Admin\Controller;

use App\Admin\Controller\UserAuthenticationCrudController;
use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
        yield ChoiceField::new('header', 'En tête')->setChoices([
            'COMMENT' => 'comment',
            'NEW' => 'new',
            'GIFT' => 'gift',
            'ACCOUNT' => 'account'
        ]);
        yield TextField::new('action', 'Action')->hideOnIndex();
        yield AssociationField::new('recipients', 'Utilisateurs')->setCrudController(UserAuthenticationCrudController::class);
        yield ArrayField::new('views', 'Vue')->hideOnForm()->hideOnIndex();
        yield TextareaField::new('content', 'Contenu');
        yield DateTimeField::new('sentAt', 'Envoyé(e) le');

        yield DateTimeField::new('updatedAt', 'Modifié(e) le')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Créé(e) le')->hideOnForm()->hideOnIndex();
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
