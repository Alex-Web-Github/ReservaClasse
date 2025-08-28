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
        //return parent::index();
        // when using legacy admin URLs, use the URL generator to build the needed URL
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // Option 1. Make your dashboard redirect to the same page for all users
        return $this->redirect($adminUrlGenerator->setController(SlotCrudController::class)->generateUrl());

        // Option 2. Make your dashboard redirect to different pages depending on the user
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ReservaClasse V2');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Les Sessions d\'entretien', 'fas fa-list', Session::class);
        yield MenuItem::linkToCrud('Les jours de RdV', 'fas fa-list', DateSession::class);
        yield MenuItem::linkToCrud('Les Créneaux', 'fas fa-list', Slot::class);
        yield MenuItem::linkToCrud('Les Elèves', 'fas fa-list', Eleve::class);
        yield MenuItem::linkToCrud('Les Profs', 'fas fa-list', User::class);
    }
}
