<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-teacher',
    description: 'Crée un nouvel utilisateur enseignant',
)]
class CreateTeacherCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'enseignant')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'enseignant')
            ->addArgument('fullname', InputArgument::REQUIRED, 'Nom complet de l\'enseignant')
            ->addArgument('admincode', InputArgument::REQUIRED, 'Code personnel de l\'enseignant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $fullname = $input->getArgument('fullname');
        $admincode = $input->getArgument('admincode');

        // Vérifier si l'email existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà.');
            return Command::FAILURE;
        }

        // Vérifier si le code admin existe déjà
        $existingCode = $this->entityManager->getRepository(User::class)->findOneBy(['adminCode' => $admincode]);
        if ($existingCode) {
            $io->error('Ce code personnel est déjà utilisé.');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFullName($fullname);
        $user->setAdminCode($admincode);
        $user->setRoles(['ROLE_TEACHER']); // Attribution du rôle enseignant

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Enseignant "%s" créé avec succès.', $fullname));
        $io->table(
            ['Email', 'Nom', 'Code Personnel'],
            [[$email, $fullname, $admincode]]
        );

        return Command::SUCCESS;
    }
}
