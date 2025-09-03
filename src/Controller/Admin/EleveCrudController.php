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
        $deleteEndYear = Action::new('deleteEndYear', 'Suppression fin d\'année')
            ->linkToCrudAction('deleteEndYearStudents')
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
    public function deleteEndYearStudents(\EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto $batchActionDto)
    {
        $entityManager = $this->entityManager;

        foreach ($batchActionDto->getEntityIds() as $id) {
            $eleve = $entityManager->getRepository(Eleve::class)->find($id);
            if ($eleve) {
                $entityManager->remove($eleve);
            }
        }
        $this->addFlash('success', 'Les élèves sélectionnés ont été supprimés avec succès');

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }
}
