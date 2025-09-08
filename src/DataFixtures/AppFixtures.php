<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Eleve;
use App\Entity\Session;
use App\Entity\DateSession;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        // Administrateur
        $admin = new User();
        $admin->setFirstName('John');
        $admin->setLastName('TheAdmin');
        $admin->setEmail('admin@test.com');
        $hashedPasswordAdmin = $this->passwordHasher->hashPassword($admin, 'Password123');
        $admin->setPassword($hashedPasswordAdmin);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPublicCode('ADMIN123');
        $manager->persist($admin);

        // Professeur
        $teacher = new User();
        $teacher->setFirstName($faker->firstName());
        $teacher->setLastName($faker->lastName());
        $teacher->setEmail('teacher@test.com');
        $hashedPasswordTeacher = $this->passwordHasher->hashPassword($teacher, 'Password123');
        $teacher->setPassword($hashedPasswordTeacher);
        $teacher->setRoles(['ROLE_TEACHER']);
        $teacher->setPublicCode('TEACHER123');
        $manager->persist($teacher);

        $manager->flush();

        // Élèves
        for ($i = 0; $i < 10; $i++) {
            $eleve = new Eleve();
            $eleve->setFirstName($faker->firstName());
            $eleve->setLastName($faker->lastName());
            $eleve->setUser($teacher); // Associer l'élève au professeur
            $manager->persist($eleve);
        }

        // Sessions
        for ($i = 0; $i < 5; $i++) {
            $session = new Session();
            $session->setLabel(sprintf('Session %d', $i + 1));
            $session->setSlotDuration(30);
            $session->setSlotInterval($faker->numberBetween(0, 15));
            $session->setUser($teacher); // Associer la session au professeur
            $manager->persist($session);
        }
        $manager->flush();

        // Journées pour chaque Session
        for ($i = 0; $i < 5; $i++) {
            $session = $manager->getRepository(Session::class)->find($i + 1);
            $dateSession = new DateSession();
            $dateSession->setSession($session);
            $dateSession->setDate($faker->dateTimeBetween('+1 day', '+10 days'));
            $dateSession->setStartTime(DateTime::createFromFormat('H:i', '16:00'));
            $dateSession->setEndTime(DateTime::createFromFormat('H:i', '18:00'));

            $manager->persist($dateSession);
        }
        $manager->flush();
    }
}
