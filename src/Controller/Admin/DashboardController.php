<?php

namespace App\Controller\Admin;

use App\Entity\DateSession;
use App\Entity\Slot;
use App\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AdminDashboard(routePath: '/admin/{admin_code}', routeName: 'admin_teacher')]
class DashboardController extends AbstractDashboardController
{
    // Redirection '/admin' générique vers la page d'accueil
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    // Accès au Dashboard uniquement pour les enseignants avec un code admin valide
    #[Route('/admin/{admin_code}', name: 'admin_teacher')]
    public function manage(string $admin_code, Request $request, EntityManagerInterface $em): Response
    {
        $prof = $em->getRepository(User::class)->findOneBy(['adminCode' => $admin_code]);
        if (!$prof) {
            throw $this->createNotFoundException("Accès interdit");
        }

        //return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator
            ->setController(SlotCrudController::class)
            ->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ReservaClasse V2');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Les Créneaux', 'fas fa-list', Slot::class);
        yield MenuItem::linkToCrud('Les élèves', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Les Sessions', 'fas fa-list', Session::class);
        yield MenuItem::linkToCrud('Les jours de RdV', 'fas fa-list', DateSession::class);
    }
}
