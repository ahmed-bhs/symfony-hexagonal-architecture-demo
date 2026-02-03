<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Catalog\Domain\ValueObject;

use App\Order\Catalog\Domain\ValueObject\ProductId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class ProductIdTest extends TestCase
{
    public function testCreateWithValidUuid(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $productId = new ProductId($uuid);

        $this->assertSame($uuid, $productId->value());
    }

    public function testCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product ID cannot be empty');

        new ProductId('');
    }

    public function testCannotBeInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Product ID format');

        new ProductId('not-a-uuid');
    }

    public function testEquals(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $productId1 = new ProductId($uuid);
        $productId2 = new ProductId($uuid);
        $productId3 = new ProductId(Uuid::v4()->toRfc4122());

        $this->assertTrue($productId1->equals($productId2));
        $this->assertFalse($productId1->equals($productId3));
    }

    public function testToString(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $productId = new ProductId($uuid);

        $this->assertSame($uuid, (string) $productId);
    }
}
