<?php

declare(strict_types=1);

namespace App\Tests\Unit\Gift\Attribution\Domain\Model;

use App\Gift\Attribution\Domain\Model\Gift;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Gift Entity - MINIMUM REQUIRED.
 */
final class GiftTest extends TestCase
{
    #[Test]
    public function it_creates_gift(): void
    {
        $gift = Gift::create('gift-1', 'Vélo', 'Description', 10);

        $this->assertEquals('gift-1', $gift->getId());
        $this->assertEquals('Vélo', $gift->getName());
        $this->assertEquals(10, $gift->getQuantity());
    }

    #[Test]
    public function it_checks_if_in_stock(): void
    {
        $inStock = Gift::create('gift-1', 'Vélo', 'Description', 5);
        $outOfStock = Gift::create('gift-2', 'Ballon', 'Description', 0);

        $this->assertTrue($inStock->isInStock());
        $this->assertFalse($outOfStock->isInStock());
    }

    #[Test]
    public function it_checks_availability(): void
    {
        $gift = Gift::create('gift-1', 'Vélo', 'Description', 5);

        $this->assertTrue($gift->isAvailable(3));
        $this->assertTrue($gift->isAvailable(5));
        $this->assertFalse($gift->isAvailable(10));
    }
}
