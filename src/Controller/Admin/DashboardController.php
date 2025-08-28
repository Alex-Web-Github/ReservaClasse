<?php

namespace App\Controller\Admin;

use App\Entity\DateSession;
use App\Entity\Slot;
use App\Entity\Session;
use App\Entity\User;
use App\Entity\Eleve;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_TEACHER') && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(SlotCrudController::class)->generateUrl());

        // Option 2. Make your dashboard redirect to different pages depending on the user
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration du site');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Accueil | RéservaClasse', 'fas fa-home', 'app_home');
        yield MenuItem::section('Rendez-vous Parents');
        yield MenuItem::linkToCrud('Les Sessions d\'entretien', 'fas fa-calendar-days', Session::class);
        yield MenuItem::linkToCrud('Les jours de RdV', 'fas fa-calendar-check', DateSession::class);
        yield MenuItem::linkToCrud('Les Créneaux', 'fas fa-clock', Slot::class);
        yield MenuItem::section('La Classe');
        yield MenuItem::linkToCrud('Les Elèves', 'fas fa-graduation-cap', Eleve::class);

        // Options réservées aux administrateurs
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Les utilisateurs');
            yield MenuItem::linkToCrud('Les Profs', 'fas fa-chalkboard-teacher', User::class);
        }
    }
}
