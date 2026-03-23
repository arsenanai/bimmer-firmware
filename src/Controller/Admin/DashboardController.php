<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\SoftwareVersionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $stats = $this->container->get(SoftwareVersionRepository::class)->getDashboardStats();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            SoftwareVersionRepository::class => SoftwareVersionRepository::class,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BimmerTech Firmware Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(SoftwareVersionCrudController::class, 'Software Versions', 'fa fa-download');
    }
}
