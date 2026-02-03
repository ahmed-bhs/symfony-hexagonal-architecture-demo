<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\ValueObject;

use App\Order\Ordering\Domain\ValueObject\OrderStatus;
use PHPUnit\Framework\TestCase;

final class OrderStatusTest extends TestCase
{
    public function testCreatePending(): void
    {
        $status = OrderStatus::pending();

        $this->assertSame('pending', $status->value);
        $this->assertTrue($status->isPending());
    }

    public function testCreateConfirmed(): void
    {
        $status = OrderStatus::confirmed();

        $this->assertSame('confirmed', $status->value);
        $this->assertTrue($status->isConfirmed());
    }

    public function testCreateShipped(): void
    {
        $status = OrderStatus::shipped();

        $this->assertSame('shipped', $status->value);
        $this->assertTrue($status->isShipped());
    }

    public function testCreateDelivered(): void
    {
        $status = OrderStatus::delivered();

        $this->assertSame('delivered', $status->value);
        $this->assertTrue($status->isDelivered());
    }

    public function testCreateCancelled(): void
    {
        $status = OrderStatus::cancelled();

        $this->assertSame('cancelled', $status->value);
        $this->assertTrue($status->isCancelled());
    }

    public function testInvalidStatusThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid order status: "invalid"');

        new OrderStatus('invalid');
    }

    public function testCanTransitionFromPendingToConfirmed(): void
    {
        $pending = OrderStatus::pending();
        $confirmed = OrderStatus::confirmed();

        $this->assertTrue($pending->canTransitionTo($confirmed));
    }

    public function testCanTransitionFromPendingToCancelled(): void
    {
        $pending = OrderStatus::pending();
        $cancelled = OrderStatus::cancelled();

        $this->assertTrue($pending->canTransitionTo($cancelled));
    }

    public function testCannotTransitionFromPendingToShipped(): void
    {
        $pending = OrderStatus::pending();
        $shipped = OrderStatus::shipped();

        $this->assertFalse($pending->canTransitionTo($shipped));
    }

    public function testCanTransitionFromConfirmedToShipped(): void
    {
        $confirmed = OrderStatus::confirmed();
        $shipped = OrderStatus::shipped();

        $this->assertTrue($confirmed->canTransitionTo($shipped));
    }

    public function testCanTransitionFromConfirmedToCancelled(): void
    {
        $confirmed = OrderStatus::confirmed();
        $cancelled = OrderStatus::cancelled();

        $this->assertTrue($confirmed->canTransitionTo($cancelled));
    }

    public function testCanTransitionFromShippedToDelivered(): void
    {
        $shipped = OrderStatus::shipped();
        $delivered = OrderStatus::delivered();

        $this->assertTrue($shipped->canTransitionTo($delivered));
    }

    public function testCannotTransitionFromShippedToCancelled(): void
    {
        $shipped = OrderStatus::shipped();
        $cancelled = OrderStatus::cancelled();

        $this->assertFalse($shipped->canTransitionTo($cancelled));
    }

    public function testCannotTransitionFromDelivered(): void
    {
        $delivered = OrderStatus::delivered();
        $pending = OrderStatus::pending();

        $this->assertFalse($delivered->canTransitionTo($pending));
    }

    public function testCannotTransitionFromCancelled(): void
    {
        $cancelled = OrderStatus::cancelled();
        $pending = OrderStatus::pending();

        $this->assertFalse($cancelled->canTransitionTo($pending));
    }

    public function testIsFinal(): void
    {
        $this->assertFalse(OrderStatus::pending()->isFinal());
        $this->assertFalse(OrderStatus::confirmed()->isFinal());
        $this->assertFalse(OrderStatus::shipped()->isFinal());
        $this->assertTrue(OrderStatus::delivered()->isFinal());
        $this->assertTrue(OrderStatus::cancelled()->isFinal());
    }

    public function testEquals(): void
    {
        $status1 = OrderStatus::pending();
        $status2 = OrderStatus::pending();
        $status3 = OrderStatus::confirmed();

        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
    }

    public function testToString(): void
    {
        $status = OrderStatus::pending();

        $this->assertSame('pending', (string) $status);
    }
}
