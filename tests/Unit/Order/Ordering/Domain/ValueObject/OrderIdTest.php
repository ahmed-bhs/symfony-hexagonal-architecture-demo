<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\ValueObject;

use App\Order\Ordering\Domain\ValueObject\OrderId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class OrderIdTest extends TestCase
{
    public function testCreateWithValidUuid(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $orderId = new OrderId($uuid);

        $this->assertSame($uuid, $orderId->value());
    }

    public function testCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order ID cannot be empty');

        new OrderId('');
    }

    public function testCannotBeInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Order ID format');

        new OrderId('not-a-uuid');
    }

    public function testEquals(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $orderId1 = new OrderId($uuid);
        $orderId2 = new OrderId($uuid);
        $orderId3 = new OrderId(Uuid::v4()->toRfc4122());

        $this->assertTrue($orderId1->equals($orderId2));
        $this->assertFalse($orderId1->equals($orderId3));
    }

    public function testToString(): void
    {
        $uuid = Uuid::v4()->toRfc4122();
        $orderId = new OrderId($uuid);

        $this->assertSame($uuid, (string) $orderId);
    }
}
