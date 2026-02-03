<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Cart\Domain\ValueObject;

use App\Order\Cart\Domain\ValueObject\CartId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CartIdTest extends TestCase
{
    public function testCreateWithValidUuid(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $cartId = new CartId($uuid);

        $this->assertSame($uuid, $cartId->value());
    }

    public function testCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart ID cannot be empty');

        new CartId('');
    }

    public function testCannotBeInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Cart ID format');

        new CartId('not-a-uuid');
    }

    public function testEquals(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $cartId1 = new CartId($uuid);
        $cartId2 = new CartId($uuid);
        $cartId3 = new CartId(Uuid::v4()->toRfc4122());

        $this->assertTrue($cartId1->equals($cartId2));
        $this->assertFalse($cartId1->equals($cartId3));
    }

    public function testToString(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $cartId = new CartId($uuid);

        $this->assertSame($uuid, (string) $cartId);
    }
}
