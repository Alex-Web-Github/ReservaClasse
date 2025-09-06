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

        // Optimisation de la requête avec les jointures
        $slots = $em->getRepository(Slot::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.dateSession', 'ds')
            ->addSelect('ds')
            ->leftJoin('ds.session', 'sess')  // Ajout de la jointure avec Session
            ->addSelect('sess')               // Sélection de la session
            ->leftJoin('s.eleve', 'e')
            ->addSelect('e')
            ->orderBy('ds.date', 'ASC')
            ->addOrderBy('s.startTime', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($slots)) {
            $this->addFlash('info', 'Aucun créneau trouvé.');
        }

        // Création d'un tableau multidimensionnel eleveId => [slots]
        $eleveSlots = [];
        foreach ($slots as $slot) {
            if ($slot->getEleve()) {
                $eleveId = $slot->getEleve()->getId();
                if (!isset($eleveSlots[$eleveId])) {
                    $eleveSlots[$eleveId] = [];
                }
                $eleveSlots[$eleveId][] = $slot;
            }
        }

        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleves,
            'eleveSlots' => $eleveSlots,
        ]);
    }
}
