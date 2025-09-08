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
            return $this->redirectToRoute('session.show', ['id' => $sessionId]);
        }

        // Vérifier si un élève a été sélectionné
        $eleveId = $request->request->get('eleve');
        if (!$eleveId) {
            $this->addFlash('error', 'Veuillez sélectionner un élève.');
            return $this->redirectToRoute('session.index', ['id' => $sessionId]);
        }

        // Récupérer l'élève sélectionné
        $eleve = $em->getRepository(Eleve::class)->find($eleveId);
        if (!$eleve) {
            $this->addFlash('error', 'Élève non trouvé.');
            return $this->redirectToRoute('session.index', ['id' => $sessionId]);
        }

        try {
            // Réserver le créneau
            $slot->setEleve($eleve);
            $slot->setIsBooked(true);
            $em->flush();

            $this->addFlash('info', sprintf(
                'Inscription réussie ! %s a été inscrit(e) au créneau du %s à %s',
                $eleve->getFullName(),
                $slot->getDateSession()->getDate()->format('d/m/Y'),
                $slot->getStartTime()->format('H:i')
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la réservation.');
            return $this->redirectToRoute('session.show', ['id' => $sessionId]);
        }

        return $this->redirectToRoute('eleve.index');
    }

    public function index(EntityManagerInterface $em): Response
    {
        $eleves = $em->getRepository(Eleve::class)->findAll();

        // Récupérer les slots réservés pour chaque élève
        $slots = $em->getRepository(Slot::class)->findAll();

        // Créer un tableau associatif eleveId => slot
        $eleveSlots = [];
        foreach ($slots as $slot) {
            if ($slot->getEleve()) {
                $eleveSlots[$slot->getEleve()->getId()] = $slot;
            }
        }

        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleves,
            'eleveSlots' => $eleveSlots
        ]);
    }
}
