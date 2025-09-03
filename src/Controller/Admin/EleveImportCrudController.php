<?php

namespace App\Controller\Admin;

use App\Entity\EleveImport;
use App\Service\EleveImportService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class EleveImportCrudController extends AbstractCrudController
{
    private EleveImportService $importService;

    public function __construct(EleveImportService $importService)
    {
        $this->importService = $importService;
    }

    public static function getEntityFqcn(): string
    {
        return EleveImport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Importer des élèves')
            ->setPageTitle('index', 'Importer une liste d\'élèves');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('content', 'Liste des élèves')
            ->setHelp('Entrez un élève par ligne au format : Prénom Nom')
            ->setRequired(true);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une liste d\'élèves');
            })
            ->disable(Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour vers la liste');
    }

    public function createEntity(string $entityFqcn)
    {
        $import = new EleveImport();
        $import->setUser($this->getUser());
        return $import;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof EleveImport) {
            $this->addFlash('error', 'Instance invalide');
            return;
        }

        // Débogage de l'utilisateur
        $currentUser = $this->getUser();
        if (!$currentUser) {
            $this->addFlash('error', 'Aucun utilisateur connecté');
            return;
        }

        // Forcer l'assignation de l'utilisateur
        $entityInstance->setUser($currentUser);

        try {
            $entityManager->persist($entityInstance);
            $entityManager->flush();

            // Import des élèves seulement si la persistance a réussi
            $results = $this->importService->importFromText($entityInstance->getContent(), $currentUser);

            foreach ($results['success'] as $message) {
                $this->addFlash('success', $message);
            }

            foreach ($results['errors'] as $message) {
                $this->addFlash('warning', $message);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur de persistance : ' . $e->getMessage());
            // Log de débogage
            dump($currentUser);
            dump($entityInstance);
            throw $e;
        }
    }
}
