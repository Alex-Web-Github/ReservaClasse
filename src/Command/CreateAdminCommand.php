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
    name: 'app:create-admin',
    description: 'Création d\'un compte administrateur',
)]
class CreateAdminCommand extends Command
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

        $admin = new User();

        // Demander les informations
        $firstName = $io->ask('Prénom de l\'administrateur');
        $lastName = $io->ask('Nom de l\'administrateur');
        $email = $io->ask('Email de l\'administrateur');
        $password = $io->askHidden('Mot de passe');
        $publicCode = $io->ask('Code public (optionnel)');

        // Configurer l'administrateur
        $admin->setFirstName($firstName);
        $admin->setLastName($lastName);
        $admin->setEmail($email);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $admin->setRoles(['ROLE_ADMIN']);

        if ($publicCode) {
            $admin->setPublicCode($publicCode);
        }

        // Persister en base de données
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Compte administrateur créé avec succès !');

        return Command::SUCCESS;
    }
}
