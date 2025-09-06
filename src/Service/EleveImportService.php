<?php

namespace App\Service;

use App\Entity\Eleve;
use App\Entity\User;
use App\Entity\Slot;
use Doctrine\ORM\EntityManagerInterface;

class EleveImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function importFromText(string $content, User $user): array
    {
        $results = ['success' => [], 'errors' => []];

        try {
            // D'abord, supprimer les réservations existantes
            $slots = $this->entityManager->getRepository(Slot::class)
                ->createQueryBuilder('s')
                ->join('s.eleve', 'e')
                ->where('e.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($slots as $slot) {
                $slot->setEleve(null);
                $slot->setIsBooked(false);
            }

            // Ensuite, supprimer les élèves
            $existingEleves = $this->entityManager->getRepository(Eleve::class)
                ->findBy(['user' => $user]);

            foreach ($existingEleves as $eleve) {
                $this->entityManager->remove($eleve);
            }

            $this->entityManager->flush();

            // Import des nouveaux élèves
            $lines = explode("\n", trim($content));

            foreach ($lines as $lineNumber => $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $nameParts = explode(' ', $line);
                if (count($nameParts) < 2) {
                    $results['errors'][] = "Ligne " . ($lineNumber + 1) . " : format incorrect";
                    continue;
                }

                // Modification ici : le premier mot est le nom de famille
                $lastName = array_shift($nameParts);
                $firstName = implode(' ', $nameParts);

                $eleve = new Eleve();
                $eleve->setFirstName($firstName);
                $eleve->setLastName($lastName);
                $eleve->setUser($user);

                $this->entityManager->persist($eleve);
                $results['success'][] = "Élève ajouté : $lastName $firstName";
            }

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $results['errors'][] = "Erreur : " . $e->getMessage();
        }

        return $results;
    }
}
