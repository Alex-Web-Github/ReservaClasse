<?php

namespace App\Controller;

use App\Repository\SlotsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SlotsController extends AbstractController
{
  #[Route('/slots', name: 'page_slots')]
  public function index(SlotsRepository $repo): Response
  {
    //dd($repo->findAllOrderedByDate());
    return $this->render('slots/index.html.twig', [
      'page_title' => 'Les créneaux',
      'slots' => $repo->findAllOrderedByDate(),
    ]);
  }
}
