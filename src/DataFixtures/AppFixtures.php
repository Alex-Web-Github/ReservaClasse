<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Eleve;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Professeur
        $prof = new User();
        $prof->setFullName('Félicie Foulc');
        $prof->setEmail('prof@demo.fr');
        $prof->setAdminCode('ADMIN123');
        $manager->persist($prof);

        // Élèves (année scolaire 2025-2026)
        $eleves = [
            'Alice Dupont',
            'Léo Martin',
            'Sophie Durand',
            'Thomas Bernard',
            'Camille Petit',
        ];

        foreach ($eleves as $name) {
            $eleve = new Eleve();
            $eleve->setUser($prof);
            $eleve->setFullName($name);
            $eleve->setSchoolYear('2025-2026');
            $manager->persist($eleve);
        }

        $manager->flush();
    }
}
