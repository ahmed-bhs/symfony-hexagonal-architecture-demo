<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\Model;

use App\Order\Ordering\Domain\Exception\InvalidOrderStateException;
use App\Order\Ordering\Domain\Exception\OrderAlreadyCancelledException;
use App\Order\Ordering\Domain\Model\Order;
use App\Order\Ordering\Domain\Model\OrderItem;
use App\Order\Ordering\Domain\ValueObject\ShippingAddress;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class OrderTest extends TestCase
{
    private function createShippingAddress(): ShippingAddress
    {
        return new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );
    }

    private function createOrderItem(): OrderItem
    {
        return OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 1,
        );
    }

    public function testPlaceOrder(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $customerId = Uuid::v4()->toRfc4122();
        $item = $this->createOrderItem();

        $order = Order::place(
            id: $id,
            customerId: $customerId,
            customerEmail: 'customer@example.com',
            items: [$item],
            shippingAddress: $this->createShippingAddress(),
        );

        $this->assertSame($id, $order->getId());
        $this->assertSame($customerId, $order->getCustomerId());
        $this->assertSame('customer@example.com', $order->getCustomerEmail());
        $this->assertCount(1, $order->getItems());
        $this->assertTrue($order->getStatus()->isPending());
        $this->assertNull($order->getTrackingNumber());
        $this->assertNull($order->getCancellationReason());
    }

    public function testPlaceOrderCalculatesTotal(): void
    {
        $item1 = OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Product 1',
            unitPrice: 10000,
            quantity: 2,
        );

        $item2 = OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Product 2',
            unitPrice: 5000,
            quantity: 3,
        );

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$item1, $item2],
            shippingAddress: $this->createShippingAddress(),
        );

        $this->assertSame(35000, $order->getTotalAmount()->amount);
    }

    public function testCannotPlaceOrderWithNoItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order must have at least one item');

        Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [],
            shippingAddress: $this->createShippingAddress(),
        );
    }

    public function testConfirmOrder(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();

        $this->assertTrue($order->getStatus()->isConfirmed());
    }

    public function testShipOrder(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('TRACK123456');

        $this->assertTrue($order->getStatus()->isShipped());
        $this->assertSame('TRACK123456', $order->getTrackingNumber());
    }

    public function testCannotShipWithEmptyTrackingNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Tracking number cannot be empty');

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('');
    }

    public function testDeliverOrder(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('TRACK123456');
        $order->deliver();

        $this->assertTrue($order->getStatus()->isDelivered());
        $this->assertTrue($order->getStatus()->isFinal());
    }

    public function testCancelOrder(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->cancel('Customer requested cancellation');

        $this->assertTrue($order->getStatus()->isCancelled());
        $this->assertSame('Customer requested cancellation', $order->getCancellationReason());
    }

    public function testCannotCancelAlreadyCancelledOrder(): void
    {
        $this->expectException(OrderAlreadyCancelledException::class);

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->cancel('First cancellation');
        $order->cancel('Second cancellation');
    }

    public function testCannotConfirmShippedOrder(): void
    {
        $this->expectException(InvalidOrderStateException::class);

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('TRACK123');
        $order->confirm();
    }

    public function testCannotShipPendingOrder(): void
    {
        $this->expectException(InvalidOrderStateException::class);

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->ship('TRACK123');
    }

    public function testCannotCancelShippedOrder(): void
    {
        $this->expectException(InvalidOrderStateException::class);

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('TRACK123');
        $order->cancel('Too late');
    }

    public function testGetItemCount(): void
    {
        $item1 = $this->createOrderItem();
        $item2 = $this->createOrderItem();

        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$item1, $item2],
            shippingAddress: $this->createShippingAddress(),
        );

        $this->assertSame(2, $order->getItemCount());
    }

    public function testDomainEventsAreRecorded(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $events = $order->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertSame('App\Order\Ordering\Domain\Event\OrderPlaced', $events[0]::class);
    }

    public function testMultipleDomainEventsAreRecorded(): void
    {
        $order = Order::place(
            id: Uuid::v4()->toRfc4122(),
            customerId: Uuid::v4()->toRfc4122(),
            customerEmail: 'customer@example.com',
            items: [$this->createOrderItem()],
            shippingAddress: $this->createShippingAddress(),
        );

        $order->confirm();
        $order->ship('TRACK123');

        $events = $order->pullDomainEvents();

        $this->assertCount(3, $events);
    }
}
