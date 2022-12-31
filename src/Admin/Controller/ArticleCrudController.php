<?php

namespace App\Admin\Controller;

use App\Admin\Controller\CommentCrudController;
use App\Admin\Controller\UserCrudController;
use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?Article $config, ?string $pageName) => $config ? '"' . $config->__toString() . '"' : 'Article'
            )
            ->setEntityLabelInPlural('Articles')
            ->setDefaultSort(['createAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('author', 'Auteur')->hideOnIndex()->setCrudController(UserCrudController::class);
        yield AssociationField::new('suggestedBy', 'Suggeré par')->hideOnIndex()->setCrudController(UserCrudController::class);
        yield TextField::new('title', 'Titre');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('title')->hideOnIndex();
        yield TextField::new('subject', 'Sujet');
        yield TextField::new('description', 'Description')->hideOnIndex();
        yield NumberField::new('level', 'Niveau');
        yield CodeEditorField::new('content', 'Contenu')->setLanguage('twig');
        yield NumberField::new('views', 'Vue(s)');
        yield AssociationField::new('comments', 'Commentaires')->setCrudController(CommentCrudController::class)->onlyOnIndex();
        yield DateTimeField::new('publishedAt', 'Publié le')->renderAsChoice();
        yield ImageField::new('image', 'image')->hideOnIndex()->hideWhenUpdating()->setUploadDir('public/data/article/images/')->setBasePath('data/article/images/')->setUploadedFileNamePattern('[slug].[extension]');
        yield CollectionField::new('comments', 'Commentaires')->useEntryCrudForm(CommentCrudController::class)->hideOnIndex();
        yield DateTimeField::new('updateAt', 'Modifié(e) le')->hideWhenCreating()->hideWhenUpdating();
        yield DateTimeField::new('createAt', 'Créé(e) le')->hideWhenCreating()->hideWhenUpdating()->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
