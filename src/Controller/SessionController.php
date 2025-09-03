<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\DateSession;
use App\Entity\Eleve;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{

    #[Route('/sessions', name: 'session.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $sessions = $em->getRepository(Session::class)->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/sessions/session-{id}/{filter}', name: 'session.show', defaults: ['filter' => 'all'])]
    public function all_slots_show(int $id, string $filter, EntityManagerInterface $em): Response
    {
        $sessionById = $em->getRepository(Session::class)->find($id);
        if (!$sessionById) {
            throw $this->createNotFoundException('Session non trouvée');
        }

        $dateSessionBySessionId = $em->getRepository(DateSession::class)->findBy(['session' => $sessionById]);
        if (empty($dateSessionBySessionId)) {
            $this->addFlash('warning', 'Aucune date programmée pour cette session');
        }

        $eleves = $em->getRepository(Eleve::class)->findAll();
        if (empty($eleves)) {
            $this->addFlash('warning', 'La liste des élèves n\'est pas renseignée.');
        }

        return $this->render('session/all_slots.html.twig', [
            'sessionId' => $sessionById,
            'dateSessions' => $dateSessionBySessionId,
            'eleves' => $eleves,
            'filter' => $filter,
        ]);
    }
}
