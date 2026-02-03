<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Cart\Domain\Model;

use App\Order\Cart\Domain\Exception\CartItemNotFoundException;
use App\Order\Cart\Domain\Model\Cart;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CartTest extends TestCase
{
    public function testCreateCart(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $customerId = Uuid::v4()->toRfc4122();

        $cart = Cart::create($id, $customerId);

        $this->assertSame($id, $cart->getId());
        $this->assertSame($customerId, $cart->getCustomerId());
        $this->assertEmpty($cart->getItems());
        $this->assertTrue($cart->isEmpty());
    }

    public function testAddItem(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $productId = Uuid::v4()->toRfc4122();

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 2,
        );

        $this->assertCount(1, $cart->getItems());
        $this->assertFalse($cart->isEmpty());
        $this->assertTrue($cart->hasProduct($productId));
    }

    public function testAddItemIncreasesQuantityIfProductExists(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $productId = Uuid::v4()->toRfc4122();

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 2,
        );

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 3,
        );

        $this->assertCount(1, $cart->getItems());
        $this->assertSame(5, $cart->getItems()[0]->getQuantity());
    }

    public function testAddMultipleDifferentProducts(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 1,
        );

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'MacBook Pro',
            unitPrice: 249900,
            quantity: 1,
        );

        $this->assertCount(2, $cart->getItems());
    }

    public function testRemoveItem(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $productId = Uuid::v4()->toRfc4122();

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 2,
        );

        $cart->removeItem($productId);

        $this->assertTrue($cart->isEmpty());
        $this->assertFalse($cart->hasProduct($productId));
    }

    public function testRemoveNonExistentItemThrows(): void
    {
        $this->expectException(CartItemNotFoundException::class);

        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $cart->removeItem(Uuid::v4()->toRfc4122());
    }

    public function testClear(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 119900,
            quantity: 2,
        );

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'MacBook Pro',
            unitPrice: 249900,
            quantity: 1,
        );

        $cart->clear();

        $this->assertTrue($cart->isEmpty());
        $this->assertEmpty($cart->getItems());
    }

    public function testGetTotal(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 100000,
            quantity: 2,
        );

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'MacBook Pro',
            unitPrice: 200000,
            quantity: 1,
        );

        $this->assertSame(400000, $cart->getTotal());
    }

    public function testGetTotalOnEmptyCart(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $this->assertSame(0, $cart->getTotal());
    }

    public function testGetItemCount(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 100000,
            quantity: 2,
        );

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'MacBook Pro',
            unitPrice: 200000,
            quantity: 3,
        );

        $this->assertSame(5, $cart->getItemCount());
    }

    public function testHasProduct(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $productId = Uuid::v4()->toRfc4122();
        $otherProductId = Uuid::v4()->toRfc4122();

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 100000,
            quantity: 1,
        );

        $this->assertTrue($cart->hasProduct($productId));
        $this->assertFalse($cart->hasProduct($otherProductId));
    }

    public function testDomainEventsAreRecorded(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());
        $productId = Uuid::v4()->toRfc4122();

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: $productId,
            productName: 'iPhone 15 Pro',
            unitPrice: 100000,
            quantity: 1,
        );

        $events = $cart->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertSame('App\Order\Cart\Domain\Event\ItemAddedToCart', $events[0]::class);
    }

    public function testPullDomainEventsClearsEvents(): void
    {
        $cart = Cart::create(Uuid::v4()->toRfc4122(), Uuid::v4()->toRfc4122());

        $cart->addItem(
            itemId: Uuid::v4()->toRfc4122(),
            productId: Uuid::v4()->toRfc4122(),
            productName: 'iPhone 15 Pro',
            unitPrice: 100000,
            quantity: 1,
        );

        $cart->pullDomainEvents();
        $events = $cart->pullDomainEvents();

        $this->assertEmpty($events);
    }
}
