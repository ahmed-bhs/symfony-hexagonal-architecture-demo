<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\ValueObject;

use App\Order\Ordering\Domain\ValueObject\OrderTotal;
use PHPUnit\Framework\TestCase;

final class OrderTotalTest extends TestCase
{
    public function testCreateWithValidAmount(): void
    {
        $total = new OrderTotal(1500);

        $this->assertSame(1500, $total->amount);
    }

    public function testCanBeZero(): void
    {
        $total = new OrderTotal(0);

        $this->assertSame(0, $total->amount);
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order total cannot be negative');

        new OrderTotal(-100);
    }

    public function testFormat(): void
    {
        $total = new OrderTotal(15999);

        $this->assertSame('159.99 EUR', $total->format());
    }

    public function testFormatWithLargeAmount(): void
    {
        $total = new OrderTotal(1234567);

        $this->assertSame('12 345.67 EUR', $total->format());
    }

    public function testEquals(): void
    {
        $total1 = new OrderTotal(1500);
        $total2 = new OrderTotal(1500);
        $total3 = new OrderTotal(2000);

        $this->assertTrue($total1->equals($total2));
        $this->assertFalse($total1->equals($total3));
    }

    public function testToString(): void
    {
        $total = new OrderTotal(1599);

        $this->assertSame('15.99 EUR', (string) $total);
    }
}
