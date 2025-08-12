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
    #[Route('/admin/{admin_code}/sessions/new', name: 'admin_sessions_new')]
    public function new(
        string $admin_code,
        Request $request,
        EntityManagerInterface $em,
        SlotGenerator $slotGenerator
    ): Response {
        $prof = $em->getRepository('App\Entity\User')->findOneBy(['adminCode' => $admin_code]);
        if (!$prof) {
            throw $this->createNotFoundException("Accès interdit");
        }

        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->setTeacher($prof);
            $session->setPublicCode(uniqid('pub_'));
            $session->setParentCode(uniqid('par_'));

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

            return $this->redirectToRoute('admin_sessions_new', ['admin_code' => $admin_code]);
        }

        return $this->render('admin/sessions_new.html.twig', [
            'form' => $form->createView(),
            'prof' => $prof,
        ]);
    }
}
