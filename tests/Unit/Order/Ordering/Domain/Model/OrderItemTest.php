<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\Model;

use App\Order\Ordering\Domain\Model\OrderItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class OrderItemTest extends TestCase
{
    public function testCreateOrderItem(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $productId = Uuid::v4()->toRfc4122();

        $item = OrderItem::create(
            id: $id,
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 2,
        );

        $this->assertSame($id, $item->getId());
        $this->assertSame($productId, $item->getProductId());
        $this->assertSame('iPhone 15 Pro', $item->getProductName());
        $this->assertSame(119900, $item->getUnitPrice());
        $this->assertSame(2, $item->getQuantity());
    }

    public function testQuantityMustBePositive(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 0,
        );
    }

    public function testQuantityCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: -1,
        );
    }

    public function testGetSubtotal(): void
    {
        $item = OrderItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 3,
        );

        $this->assertSame(3000, $item->getSubtotal());
    }
}
