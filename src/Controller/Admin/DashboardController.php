<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Domaine;
use App\Entity\Libelle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
        // return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Challenge Stack');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Dashboard');
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Comptes', 'fas fa-user', User::class);

        yield MenuItem::section('Configurations');
        yield MenuItem::linkToCrud('Domaine', 'fas fa-newspaper', Domaine::class);
        yield MenuItem::linkToCrud('Libellé', 'fas fa-list', Libelle::class);
    }
}
