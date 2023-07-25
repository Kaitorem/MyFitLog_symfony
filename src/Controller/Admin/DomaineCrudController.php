<?php

namespace App\Controller\Admin;

use App\Entity\Domaine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DomaineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Domaine::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->update(Crud::PAGE_INDEX,Action::NEW,function(Action $action){
            return $action->setIcon('fa fa-newspaper')->addCssClass('px-3 py-2');
        })
        ->update(Crud::PAGE_INDEX,Action::EDIT,function(Action $action){
            return $action->setIcon('fa fa-edit');
        })
        ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
            return $action->setIcon('fa fa-eye');
        })
        ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
            return $action->setIcon('fa fa-trash');
        });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('libelle'),
        ];
    }
}
