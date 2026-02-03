<?php

declare(strict_types=1);

namespace App\Order\Ordering\Infrastructure\Web\Admin;

use App\Order\Ordering\Domain\Model\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Order')
            ->setEntityLabelInPlural('Orders')
            ->setSearchFields(['id', 'customerId', 'customerEmail'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('customerId')
            ->setLabel('Customer ID')
            ->hideOnIndex();

        yield EmailField::new('customerEmail')
            ->setLabel('Customer Email');

        yield TextField::new('status.value', 'Status')
            ->setTemplatePath('admin/fields/order_status.html.twig');

        yield MoneyField::new('totalAmount.amount', 'Total')
            ->setCurrency('EUR')
            ->setStoredAsCents(true);

        yield IntegerField::new('itemCount', 'Items')
            ->onlyOnIndex();

        yield TextField::new('trackingNumber')
            ->setLabel('Tracking #')
            ->hideOnIndex();

        yield TextField::new('shippingAddress', 'Shipping Address')
            ->hideOnIndex();

        yield DateTimeField::new('createdAt')
            ->setLabel('Created');
    }
}
