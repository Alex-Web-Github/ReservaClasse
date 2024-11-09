<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Reservations;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationsController extends AbstractController
{
  // Afficher toutes les réservations
  #[Route('/reservations/', name: 'page_reservations', methods: 'GET')]
  public function index(ReservationsRepository $repo): Response
  {
    //dd($repo->findAll());
    return $this->render('reservations/index.html.twig', [
      'page_title' => 'Les réservations',
      'reservations' => $repo->findAll(),
    ]);
  }

  // Créer une réservation depuis un créneau donné
  #[Route('/reservation/create/{id}', name: 'reservation_create', requirements: ['id' => '\d+'], methods: 'POST')]
  public function create(ReservationsRepository $repo, $id): Response
  {
    // dd($_POST);
    // je récupère le contenu du formulaire <select> avec le name="user"
    $user = $_POST['user'];
    $repo->createReservationFromSlotIdAndUserId($id, $user);

    return $this->redirectToRoute('page_reservations');
  }

  // TODO Supprimer une réservation depuis un créneau donné (pour un utilisateur donné)

}
