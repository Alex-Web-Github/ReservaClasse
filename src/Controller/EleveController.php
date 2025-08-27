<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Eleve;
use App\Form\ElevesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EleveController extends AbstractController
{
    #[Route('/admin/{admin_code}/eleves', name: 'admin_eleves')]
    public function manage(string $admin_code, Request $request, EntityManagerInterface $em): Response
    {
        $prof = $em->getRepository(User::class)->findOneBy(['adminCode' => $admin_code]);
        if (!$prof) {
            throw $this->createNotFoundException("Accès interdit");
        }

        // Récupérer tous les élèves de cette année scolaire
        $eleves = $em->getRepository(Eleve::class)->findBy(
            ['user' => $prof],
            ['fullName' => 'ASC']
        );

        // Préremplir le formulaire avec l'année et la liste des élèves
        $elevesNames = implode("\n", array_map(fn($e) => $e->getFullName(), $eleves));

        $form = $this->createForm(ElevesType::class, null, [
            'data' => [
                // 'schoolYear' => $currentSchoolYear,
                'elevesList' => $elevesNames,
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lines = array_filter(array_map('trim', explode("\n", $form->get('elevesList')->getData())));

            // Ajouter les nouveaux
            foreach ($lines as $name) {
                $eleve = new Eleve();
                $eleve->setUser($prof);
                $eleve->setFullName($name);
                $em->persist($eleve);
            }

            $em->flush();
            $this->addFlash('success', 'Liste des élèves mise à jour.');

            return $this->redirectToRoute('admin_eleves', ['admin_code' => $admin_code]);
        }

        return $this->render('admin/eleves.html.twig', [
            'form' => $form->createView(),
            'prof' => $prof,
            'eleves' => $eleves,
        ]);
    }
}
