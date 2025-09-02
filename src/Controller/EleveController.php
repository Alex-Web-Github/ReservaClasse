<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Slot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EleveController extends AbstractController
{

    #[Route('/eleves', name: 'eleve.index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {

        $eleves = $em->getRepository(Eleve::class)->findAll();
        if (empty($eleves)) {
            $this->addFlash('info', 'Aucun élève trouvé.');
        }

        $slots = $em->getRepository(Slot::class)->findAll();
        if (empty($slots)) {
            $this->addFlash('info', 'Aucun créneau trouvé.');
        }

        // Créer un tableau associatif eleveId => slot
        $eleveSlots = [];
        foreach ($slots as $slot) {
            if ($slot->getEleve()) {
                $eleveSlots[$slot->getEleve()->getId()] = $slot;
            }
        }

        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleves,
            'eleveSlots' => $eleveSlots,
        ]);
    }
}
