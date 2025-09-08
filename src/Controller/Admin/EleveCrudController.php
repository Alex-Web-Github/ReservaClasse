<?php

namespace App\Controller\Admin;

use App\Entity\Eleve;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class EleveCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AdminUrlGenerator $adminUrlGenerator
    ) {}

    public static function getEntityFqcn(): string
    {
        return Eleve::class;
    }

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
            ->setDefaultSort(['lastName' => 'ASC', 'firstName' => 'ASC'])
            ->setSearchFields(['lastName', 'firstName'])
            ->setAutofocusSearch()
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $deleteEndYear = Action::new('deleteEndYear', 'Supprimer tous les élèves')
            ->linkToCrudAction('deleteAllStudents')
            ->addCssClass('btn btn-danger')
            ->setIcon('fa fa-user-times')
            ->displayAsButton();

        return $actions

            ->addBatchAction($deleteEndYear);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('lastName', 'Nom')
                ->setHelp('Nom de famille de l\'élève')
                ->setFormTypeOption('attr', [
                    'maxlength' => 100,
                    'placeholder' => 'Ex: DUPONT'
                ]),
            TextField::new('firstName', 'Prénom')
                ->setHelp('Prénom de l\'élève')
                ->setFormTypeOption('attr', [
                    'maxlength' => 100,
                    'placeholder' => 'Ex: Jean'
                ]),
        ];
    }

    // Ajout de la méthode pour la suppression en masse
    public function deleteAllStudents(\EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto $batchActionDto)
    {
        $entityManager = $this->entityManager;
        $elevesDeleted = 0;
        $errors = 0;

        foreach ($batchActionDto->getEntityIds() as $id) {
            try {
                $eleve = $entityManager->getRepository(Eleve::class)->find($id);
                if ($eleve) {
                    // Récupérer tous les slots associés à cet élève
                    $slots = $entityManager->getRepository('App\Entity\Slot')
                        ->findBy(['eleve' => $eleve]);

                    // Supprimer l'association avec l'élève pour chaque slot
                    foreach ($slots as $slot) {
                        $slot->setEleve(null);
                        $slot->setIsBooked(false);
                    }

                    // Supprimer l'élève
                    $entityManager->remove($eleve);
                    $elevesDeleted++;
                }
            } catch (\Exception $e) {
                $errors++;
            }
        }

        try {
            $entityManager->flush();

            if ($elevesDeleted > 0) {
                $this->addFlash('success', sprintf('%d élève(s) ont été supprimés avec succès', $elevesDeleted));
            }
            if ($errors > 0) {
                $this->addFlash('warning', sprintf('%d élève(s) n\'ont pas pu être supprimés', $errors));
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression');
        }

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }
}
