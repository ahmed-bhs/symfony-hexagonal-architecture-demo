<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Cart\Domain\Model;

use App\Order\Cart\Domain\Model\CartItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CartItemTest extends TestCase
{
    public function testCreateCartItem(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $productId = Uuid::v4()->toRfc4122();

        $item = CartItem::create(
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

        CartItem::create(
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

        CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: -1,
        );
    }

    public function testGetSubtotal(): void
    {
        $item = CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 3,
        );

        $this->assertSame(3000, $item->getSubtotal());
    }

    public function testIncreaseQuantity(): void
    {
        $item = CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 2,
        );

        $item->increaseQuantity(3);

        $this->assertSame(5, $item->getQuantity());
    }

    public function testIncreaseQuantityWithZeroAmountThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive');

        $item = CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 2,
        );

        $item->increaseQuantity(0);
    }

    public function testIncreaseQuantityWithNegativeAmountThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive');

        $item = CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 2,
        );

        $item->increaseQuantity(-1);
    }

    public function testIsForProduct(): void
    {
        $productId = Uuid::v4()->toRfc4122();
        $otherProductId = Uuid::v4()->toRfc4122();

        $item = CartItem::create(
            id: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'Test Product',
            unitPrice: 1000,
            quantity: 2,
        );

        $this->assertTrue($item->isForProduct($productId));
        $this->assertFalse($item->isForProduct($otherProductId));
    }
}
