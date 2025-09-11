<?php

namespace App\Controller\Admin;

use App\Entity\DateSession;
use App\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Doctrine\ORM\EntityManagerInterface;

class SessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $session = new Session();
        $session->setUser($this->getUser());
        return $session;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Session) {
            return;
        }

        // Récupérer toutes les DateSessions liées
        $dateSessions = $entityManager->getRepository(DateSession::class)
            ->findBy(['session' => $entityInstance]);

        // Les détacher de la session
        foreach ($dateSessions as $dateSession) {
            $entityInstance->removeDate($dateSession);
            $entityManager->remove($dateSession);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Les Évènements')
            ->setPageTitle('new', 'Créer un évènement')
            ->setPageTitle('edit', 'Modifier un évènement')
            ->setDefaultSort(['label' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un évènement');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Créer');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Sauvegarder et retourner à la liste');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Sauvegarder et continuer les modifications');
            })
            ->disable(Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour vers la liste')
            ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('label', 'Nom de l\'évènement')
                ->setHelp('Donnez un nom à votre évènement (ex: Entretiens de rentrée)')
                ->setFormTypeOption('attr', [
                    'maxlength' => 255,
                    'placeholder' => 'Entretiens de rentrée'
                ]),
            IntegerField::new('slotDuration', 'Durée du créneau (minutes)')
                ->setHelp('Durée en minutes de chaque créneau (ex: 20 minutes)')
                ->setFormTypeOption('attr', [
                    'min' => 1,
                    'max' => 120,
                    'placeholder' => '20'
                ]),
            IntegerField::new('slotInterval', 'Intervalle entre les créneaux (minutes)')
                ->setHelp('Temps de pause entre chaque créneau (0 = pas de pause)')
                ->setFormTypeOption('attr', [
                    'min' => 0,
                    'max' => 60,
                    'placeholder' => '0'
                ]),
        ];
    }
}
