<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Cart\Domain\ValueObject;

use App\Order\Cart\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testCreateWithValidValue(): void
    {
        $quantity = new Quantity(5);

        $this->assertSame(5, $quantity->value);
    }

    public function testCannotBeZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        new Quantity(0);
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        new Quantity(-1);
    }

    public function testCannotExceed99(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity cannot exceed 99');

        new Quantity(100);
    }

    public function testMaxQuantityIsValid(): void
    {
        $quantity = new Quantity(99);

        $this->assertSame(99, $quantity->value);
    }

    public function testAdd(): void
    {
        $quantity1 = new Quantity(5);
        $quantity2 = new Quantity(3);

        $result = $quantity1->add($quantity2);

        $this->assertSame(8, $result->value);
    }

    public function testAddExceedingMaxThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity cannot exceed 99');

        $quantity1 = new Quantity(50);
        $quantity2 = new Quantity(50);

        $quantity1->add($quantity2);
    }

    public function testEquals(): void
    {
        $quantity1 = new Quantity(5);
        $quantity2 = new Quantity(5);
        $quantity3 = new Quantity(10);

        $this->assertTrue($quantity1->equals($quantity2));
        $this->assertFalse($quantity1->equals($quantity3));
    }

    public function testToString(): void
    {
        $quantity = new Quantity(42);

        $this->assertSame('42', (string) $quantity);
    }
}
