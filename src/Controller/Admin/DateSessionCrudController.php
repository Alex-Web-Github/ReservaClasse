<?php

namespace App\Controller\Admin;

use App\Entity\DateSession;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;


class DateSessionCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public static function getEntityFqcn(): string
    {
        return DateSession::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $dateSession = new DateSession();

        // Si on vient d'une session spécifique (par exemple via un lien "Ajouter une date")
        $session = $this->getContext()?->getRequest()?->query->get('session');
        if ($session) {
            $session = $this->entityManager->getRepository(Session::class)->find($session);
            if ($session && $session->getTeacher() === $this->getUser()) {
                $dateSession->setSession($session);
            }
        }

        return $dateSession;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Journées de session d\'entretien')
            ->setPageTitle('new', 'Ajouter une journée')
            ->setPageTitle('edit', 'Modifier une journée')
            ->setDefaultSort(['date' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une journée');
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
            AssociationField::new('session')
                ->setFormTypeOption('query_builder', function ($repository) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.teacher = :teacher')
                        ->setParameter('teacher', $this->getUser());
                })
                ->setRequired(true)
                ->setFormTypeOptions([
                    'placeholder' => 'Choisissez une session',
                    'attr' => ['data-validation-required-message' => 'Veuillez sélectionner une session'],
                    'help' => 'Sélectionnez la session à laquelle cette date appartient.',
                ])
                ->setLabel('Associer cette journée à une session'),
            DateField::new('date')
                ->setFormat('dd/MM/yyyy')
                ->setFormTypeOption('widget', 'single_text'),
            TimeField::new('startTime', 'Heure de début')
                ->setFormTypeOption('widget', 'single_text'),
            TimeField::new('endTime', 'Heure de fin')
                ->setFormTypeOption('widget', 'single_text'),
        ];
    }
}
