<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\DateSession;
use App\Form\SessionType;
use App\Service\SlotGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/admin/sessions/new', name: 'admin_sessions_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SlotGenerator $slotGenerator
    ): Response {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('dates')->getData() as $dateData) {
                $dateSession = new DateSession();
                $dateSession->setSession($session);
                $dateSession->setDate($dateData->getDate());
                $dateSession->setStartTime($dateData->getStartTime());
                $dateSession->setEndTime($dateData->getEndTime());

                $em->persist($dateSession);

                // Générer automatiquement les Slots
                $slotGenerator->generateForDateSession(
                    $dateSession,
                    $session->getSlotDuration(),
                    $session->getSlotInterval()
                );
            }

            $em->persist($session);
            $em->flush();

            $this->addFlash('success', 'Session créée et créneaux générés.');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/sessions_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
