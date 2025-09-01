<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\Eleve;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SlotController extends AbstractController
{
    #[Route('/slot/reserve/{id}', name: 'slot_reservation', methods: ['POST'])]
    public function reserve(Request $request, Slot $slot, EntityManagerInterface $em): Response
    {
        $sessionId = $slot->getDateSession()->getSession()->getId();

        // Vérifier si le créneau est déjà réservé
        if ($slot->getIsBooked()) {
            $this->addFlash('error', 'Ce créneau est déjà réservé.');
            return $this->redirectToRoute('session_show', ['id' => $sessionId]);
        }

        // Vérifier si un élève a été sélectionné
        $eleveId = $request->request->get('eleve');
        if (!$eleveId) {
            $this->addFlash('error', 'Veuillez sélectionner un élève.');
            return $this->redirectToRoute('session_show', ['id' => $sessionId]);
        }

        // Récupérer l'élève sélectionné
        $eleve = $em->getRepository(Eleve::class)->find($eleveId);
        if (!$eleve) {
            $this->addFlash('error', 'Élève non trouvé.');
            return $this->redirectToRoute('session_show', ['id' => $sessionId]);
        }

        try {
            // Réserver le créneau
            $slot->setEleve($eleve);
            $slot->setIsBooked(true);
            $em->flush();

            $this->addFlash('success', sprintf(
                'Créneau réservé avec succès pour %s',
                $eleve->getFullName()
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la réservation.');
        }

        return $this->redirectToRoute('session_show', ['id' => $sessionId]);
    }
}
