<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Ajouter un utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un utilisateur');
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
            IdField::new('id')->hideOnForm(),
            TextField::new('firstName', 'Prénom'),
            TextField::new('lastName', 'Nom'),
            TextField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired(false),
            TextField::new('publicCode', 'Code public')
                ->setHelp('Code optionnel pour permettre aux parents d\'élèves de réserver un créneau depuis le site web')
                ->setRequired(false),
            ChoiceField::new('roles', 'Rôle(s)')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Enseignant' => 'ROLE_TEACHER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->setFormTypeOption('expanded', true)
                ->setFormTypeOption('multiple', true)
                ->renderAsBadges(),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Récupérer l'utilisateur original de la base de données
            $originalUser = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

            // Gestion du mot de passe
            $plainPassword = $entityInstance->getPassword();
            if (empty($plainPassword)) {
                // Si aucun nouveau mot de passe n'est fourni, restaurer l'ancien
                if (isset($originalUser['password'])) {
                    $entityInstance->setPassword($originalUser['password']);
                }
            } else {
                // Si un nouveau mot de passe est fourni, le hasher
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $plainPassword
                );
                $entityInstance->setPassword($hashedPassword);
            }

            // Gestion des rôles
            $selectedRole = $entityInstance->getRoles();
            if (!is_array($selectedRole)) {
                $selectedRole = [$selectedRole];
            }
            $entityInstance->setRoles(array_unique(array_filter($selectedRole)));

            // Vérifier si c'est l'utilisateur connecté
            if ($token = $this->tokenStorage->getToken()) {
                $user = $token->getUser();
                if ($user instanceof User && $user->getId() === $entityInstance->getId()) {
                    // Mettre à jour le token avec les nouveaux rôles
                    $updatedToken = new UsernamePasswordToken(
                        $entityInstance,
                        'main', // votre firewall name
                        $entityInstance->getRoles()
                    );
                    $this->tokenStorage->setToken($updatedToken);
                }
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Gestion du mot de passe
            if ($plainPassword = $entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $plainPassword
                );
                $entityInstance->setPassword($hashedPassword);
            }

            // Gestion des rôles
            $selectedRole = $entityInstance->getRoles();
            if (!is_array($selectedRole)) {
                $selectedRole = [$selectedRole];
            }
            // S'assurer qu'on a un tableau valide avec au moins ROLE_USER
            $entityInstance->setRoles(array_unique(array_filter($selectedRole)));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
