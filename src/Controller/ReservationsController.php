<?php

namespace App\Controller;

use App\Repository\ReservationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationsController extends AbstractController
{
  // Afficher toutes les réservations
  #[Route('/reservations/', name: 'page_reservations')]
  public function index(ReservationsRepository $repo): Response
  {
    //dd($repo->findAll());
    return $this->render('reservations/index.html.twig', [
      'page_title' => 'Les réservations',
      'reservations' => $repo->findAll(),
    ]);
  }
}
