<?php

namespace App\Controller;

use App\Entity\Reservations;
use App\Repository\ReservationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationsController extends AbstractController
{
  // Afficher toutes les réservations
  #[Route('/reservations/', name: 'reservations_index', methods: ['GET'])]
  public function index(ReservationsRepository $repo): Response
  {
    //dd(__METHOD__);
    return $this->render('reservations/index.html.twig', [
      'reservations' => $repo->findAll(),
    ]);
  }
}
