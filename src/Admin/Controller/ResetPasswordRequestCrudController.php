<?php

namespace App\Admin\Controller;

use App\Authentication\Entity\ResetPasswordRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ResetPasswordRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResetPasswordRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Reset Password Request')
            ->setEntityLabelInPlural('Reset Password Requests')
            ->setDefaultSort(['requestedAt' => 'DESC']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield AssociationField::new('user', 'Auth');
        yield TextField::new('selector', 'Token');
        yield TextField::new('hashedToken', 'Token');
        yield DateTimeField::new('requestedAt', 'Demandé(e) le');
        yield DateTimeField::new('expiresAt', 'Expire le');
    }
}
