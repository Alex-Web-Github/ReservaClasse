<?php

namespace App\Controller\Admin;

use App\Entity\Eleve;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class EleveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Eleve::class;
    }

    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createEntity(string $entityFqcn)
    {
        $eleve = new Eleve();
        $eleve->setUser($this->getUser());
        return $eleve;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Élèves')
            ->setPageTitle('new', 'Ajouter un élève')
            ->setPageTitle('edit', 'Modifier un élève')
            ->setDefaultSort(['fullName' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un élève');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Créer');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Sauvegarder');
            })
            ->disable(Action::SAVE_AND_ADD_ANOTHER)
            ->disable(Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour vers la liste')
            ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName', 'Nom complet')
                ->setHelp('Nom et prénom de l\'élève')
                ->setFormTypeOption('attr', [
                    'maxlength' => 255,
                    'placeholder' => 'Ex: Dupont Jean'
                ]),
        ];
    }
}
