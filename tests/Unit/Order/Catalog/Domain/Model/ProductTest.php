<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Catalog\Domain\Model;

use App\Order\Catalog\Domain\Exception\InsufficientStockException;
use App\Order\Catalog\Domain\Model\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ProductTest extends TestCase
{
    public function testCreateProduct(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $product = Product::create(
            id: $id,
            name: 'iPhone 15 Pro',
            description: 'Latest Apple smartphone',
            priceAmount: 119900,
            stock: 50,
        );

        $this->assertSame($id, $product->getId());
        $this->assertSame('iPhone 15 Pro', (string) $product->getName());
        $this->assertSame('Latest Apple smartphone', $product->getDescription());
        $this->assertSame(119900, $product->getPrice()->amount);
        $this->assertSame(50, $product->getStock());
    }

    public function testStockCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock cannot be negative');

        Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: -1,
        );
    }

    public function testIsInStock(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $this->assertTrue($product->isInStock());
    }

    public function testIsNotInStockWhenZero(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 0,
        );

        $this->assertFalse($product->isInStock());
    }

    public function testIsAvailable(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $this->assertTrue($product->isAvailable(5));
        $this->assertTrue($product->isAvailable(10));
        $this->assertFalse($product->isAvailable(11));
    }

    public function testDecreaseStock(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $product->decreaseStock(3);

        $this->assertSame(7, $product->getStock());
    }

    public function testDecreaseStockWithZeroQuantityThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $product->decreaseStock(0);
    }

    public function testDecreaseStockWithNegativeQuantityThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $product->decreaseStock(-1);
    }

    public function testDecreaseStockWithInsufficientStockThrows(): void
    {
        $this->expectException(InsufficientStockException::class);

        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 5,
        );

        $product->decreaseStock(10);
    }

    public function testIncreaseStock(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $product->increaseStock(5);

        $this->assertSame(15, $product->getStock());
    }

    public function testIncreaseStockWithZeroQuantityThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: 'Description',
            priceAmount: 1000,
            stock: 10,
        );

        $product->increaseStock(0);
    }

    public function testDescriptionIsTrimmed(): void
    {
        $product = Product::create(
            id: Uuid::v4()->toRfc4122(),
            name: 'Test Product',
            description: '  Some description  ',
            priceAmount: 1000,
            stock: 10,
        );

        $this->assertSame('Some description', $product->getDescription());
    }
}
