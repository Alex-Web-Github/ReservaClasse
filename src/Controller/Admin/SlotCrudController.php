<?php

namespace App\Controller\Admin;

use App\Entity\Slot;
use App\Entity\Eleve;
use App\Entity\DateSession;
use App\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class SlotCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $slot = new Slot();
        $slot->setIsBooked(false);
        return $slot;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Les rendez-vous')
            ->setPageTitle('new', 'Créer un rendez-vous')
            ->setPageTitle('edit', 'Modifier un rendez-vous')
            ->setDefaultSort(['dateSession.date' => 'ASC', 'startTime' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
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
            AssociationField::new('dateSession', 'Session')
                ->hideOnForm()
                ->setFormTypeOption('disabled', true)  // Alternative à hideOnForm
                ->formatValue(function ($value, $entity) {
                    return $entity->getDateSession()->getSession() ? $entity->getDateSession()->getSession() : '';
                }),
            TimeField::new('startTime', 'Heure de début')
                ->setFormTypeOption('widget', 'single_text'),
            TimeField::new('endTime', 'Heure de fin')
                ->setFormTypeOption('widget', 'single_text'),
            BooleanField::new('isBooked', 'Réservé')
                ->hideOnForm(),
            AssociationField::new('eleve', 'Élève')
                ->setFormTypeOption('choice_label', 'fullName')
                ->formatValue(function ($value, $entity) {
                    return $entity->getEleve() ? $entity->getEleve()->getFullName() : '';
                })
                ->setHelp('Sélectionnez l\'élève pour ce créneau'),
            AssociationField::new('dateSession', 'Date')
                ->hideOnForm()
                ->setFormTypeOption('disabled', true)  // Alternative à hideOnForm
                ->formatValue(function ($value, $entity) {
                    return $entity->getDateSession() ? $entity->getDateSession()->getDate()->format('d/m/Y') : '';
                })
        ];
    }
}
