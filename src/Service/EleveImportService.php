<?php

namespace App\Service;

use App\Entity\Eleve;
use App\Entity\User;
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
            // Supprimer tous les élèves existants
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

                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);

                $eleve = new Eleve();
                $eleve->setFirstName($firstName);
                $eleve->setLastName($lastName);
                $eleve->setUser($user);

                $this->entityManager->persist($eleve);
                $results['success'][] = "Élève ajouté : $firstName $lastName";
            }

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $results['errors'][] = "Erreur : " . $e->getMessage();
        }

        return $results;
    }
}
