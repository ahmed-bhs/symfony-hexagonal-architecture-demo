<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Catalog\Domain\ValueObject;

use App\Order\Catalog\Domain\ValueObject\ProductName;
use PHPUnit\Framework\TestCase;

final class ProductNameTest extends TestCase
{
    public function testCreateWithValidName(): void
    {
        $name = new ProductName('iPhone 15 Pro');

        $this->assertSame('iPhone 15 Pro', $name->value);
    }

    public function testCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        new ProductName('');
    }

    public function testCannotBeWhitespaceOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        new ProductName('   ');
    }

    public function testCannotBeTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name must be at least 2 characters');

        new ProductName('A');
    }

    public function testMinimumLengthIsValid(): void
    {
        $name = new ProductName('AB');

        $this->assertSame('AB', $name->value);
    }

    public function testCannotBeTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot exceed 200 characters');

        new ProductName(str_repeat('A', 201));
    }

    public function testMaximumLengthIsValid(): void
    {
        $longName = str_repeat('A', 200);
        $name = new ProductName($longName);

        $this->assertSame($longName, $name->value);
    }

    public function testEquals(): void
    {
        $name1 = new ProductName('iPhone 15 Pro');
        $name2 = new ProductName('iPhone 15 Pro');
        $name3 = new ProductName('Samsung Galaxy');

        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }

    public function testToString(): void
    {
        $name = new ProductName('iPhone 15 Pro');

        $this->assertSame('iPhone 15 Pro', (string) $name);
    }
}
