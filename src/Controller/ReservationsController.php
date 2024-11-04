<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationsController extends AbstractController
{
  // Afficher toutes les réservations
  #[Route('/reservations', name: 'app_reservations')]
  public function index(): Response
  {
    return $this->render('reservations/index.html.twig', [
      'controller_name' => 'ReservationsController',
    ]);
  }
}
