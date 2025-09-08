<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-teacher',
    description: 'Création d\'un compte enseignant',
)]
class CreateTeacherCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $teacher = new User();

        // Demander les informations
        $firstName = $io->ask('Prénom de l\'enseignant');
        $lastName = $io->ask('Nom de l\'enseignant');
        $email = $io->ask('Email de l\'enseignant');
        $password = $io->askHidden('Mot de passe');
        $publicCode = $io->ask('Code public (optionnel)');

        // Vérification de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('L\'adresse email n\'est pas valide.');
            return Command::FAILURE;
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà.');
            return Command::FAILURE;
        }

        // Vérifier si le code public existe déjà
        if ($publicCode) {
            $existingCode = $this->entityManager->getRepository(User::class)->findOneBy(['publicCode' => $publicCode]);
            if ($existingCode) {
                $io->error('Ce code public est déjà utilisé.');
                return Command::FAILURE;
            }
        }

        // Configurer l'enseignant
        $teacher->setFirstName($firstName);
        $teacher->setLastName($lastName);
        $teacher->setEmail($email);
        $teacher->setPassword($this->passwordHasher->hashPassword($teacher, $password));
        $teacher->setRoles(['ROLE_TEACHER']);

        if ($publicCode) {
            $teacher->setPublicCode($publicCode);
        }

        try {
            // Persister en base de données
            $this->entityManager->persist($teacher);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error('Une erreur est survenue lors de la création de l\'enseignant : ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Compte enseignant créé avec succès !');

        // Afficher un résumé des informations
        $io->table(
            ['Email', 'Prénom', 'Nom', 'Code Public', 'Rôle'],
            [[$email, $firstName, $lastName, $publicCode ?: 'Non défini', 'ROLE_TEACHER']]
        );

        return Command::SUCCESS;
    }
}
