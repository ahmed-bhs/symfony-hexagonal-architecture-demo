<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Catalog\Domain\ValueObject;

use App\Order\Catalog\Domain\ValueObject\Price;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    public function testCreateWithValidAmount(): void
    {
        $price = new Price(1500);

        $this->assertSame(1500, $price->amount);
    }

    public function testCanBeZero(): void
    {
        $price = new Price(0);

        $this->assertSame(0, $price->amount);
        $this->assertTrue($price->isZero());
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Price(-100);
    }

    public function testAdd(): void
    {
        $price1 = new Price(1000);
        $price2 = new Price(500);

        $result = $price1->add($price2);

        $this->assertSame(1500, $result->amount);
    }

    public function testMultiply(): void
    {
        $price = new Price(1000);

        $result = $price->multiply(3);

        $this->assertSame(3000, $result->amount);
    }

    public function testIsZero(): void
    {
        $zeroPrice = new Price(0);
        $nonZeroPrice = new Price(100);

        $this->assertTrue($zeroPrice->isZero());
        $this->assertFalse($nonZeroPrice->isZero());
    }

    public function testEquals(): void
    {
        $price1 = new Price(1000);
        $price2 = new Price(1000);
        $price3 = new Price(500);

        $this->assertTrue($price1->equals($price2));
        $this->assertFalse($price1->equals($price3));
    }

    public function testFormat(): void
    {
        $price = new Price(1599);

        $this->assertSame('15.99 EUR', $price->format());
    }

    public function testToString(): void
    {
        $price = new Price(1599);

        $this->assertSame('15.99 EUR', (string) $price);
    }
}
