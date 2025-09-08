<?php

namespace App\Controller\Admin;

use App\Entity\DateSession;
use App\Entity\Slot;
use App\Entity\Session;
use App\Entity\User;
use App\Entity\Eleve;
use App\Entity\EleveImport;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;


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
        yield MenuItem::linkToUrl('Accueil | RéservaClasse', 'fas fa-home', '/');
        yield MenuItem::section('Gestion des Réservations');
        yield MenuItem::linkToCrud('Les Sessions', 'fas fa-calendar-days', Session::class);
        yield MenuItem::linkToCrud('Les Journées', 'fas fa-calendar-check', DateSession::class);
        yield MenuItem::linkToCrud('Les Créneaux', 'fas fa-clock', Slot::class);
        yield MenuItem::section('Ma Classe');
        yield MenuItem::linkToCrud('Les Elèves', 'fas fa-graduation-cap', Eleve::class);
        yield MenuItem::linkToCrud('Importer une liste d\'élèves', 'fas fa-file-import', EleveImport::class)->setAction(Action::NEW);

        // Options réservées aux administrateurs
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Les utilisateurs');
            yield MenuItem::linkToCrud('Les Profs', 'fas fa-chalkboard-teacher', User::class);
        }
    }
}
