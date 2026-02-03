<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Web\Admin;

use App\Order\Catalog\Domain\Model\Product;
use App\Order\Catalog\Infrastructure\Web\Admin\ProductCrudController;
use App\Order\Ordering\Domain\Model\Order;
use App\Order\Ordering\Infrastructure\Web\Admin\OrderCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Order Management')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Catalog');
        yield MenuItem::linkToCrud('Products', 'fa fa-box', Product::class)
            ->setController(ProductCrudController::class);

        yield MenuItem::section('Orders');
        yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', Order::class)
            ->setController(OrderCrudController::class);
    }
}
