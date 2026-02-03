<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Ordering\Domain\ValueObject;

use App\Order\Ordering\Domain\ValueObject\ShippingAddress;
use PHPUnit\Framework\TestCase;

final class ShippingAddressTest extends TestCase
{
    public function testCreateWithValidData(): void
    {
        $address = new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );

        $this->assertSame('123 Main Street', $address->street);
        $this->assertSame('Paris', $address->city);
        $this->assertSame('75001', $address->postalCode);
        $this->assertSame('France', $address->country);
    }

    public function testStreetCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Street cannot be empty');

        new ShippingAddress(
            street: '',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );
    }

    public function testStreetCannotBeWhitespaceOnly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Street cannot be empty');

        new ShippingAddress(
            street: '   ',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );
    }

    public function testCityCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('City cannot be empty');

        new ShippingAddress(
            street: '123 Main Street',
            city: '',
            postalCode: '75001',
            country: 'France',
        );
    }

    public function testPostalCodeCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Postal code cannot be empty');

        new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '',
            country: 'France',
        );
    }

    public function testCountryCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Country cannot be empty');

        new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: '',
        );
    }

    public function testFullAddress(): void
    {
        $address = new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );

        $this->assertSame('123 Main Street, 75001 Paris, France', $address->fullAddress());
    }

    public function testEquals(): void
    {
        $address1 = new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );

        $address2 = new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );

        $address3 = new ShippingAddress(
            street: '456 Other Street',
            city: 'Lyon',
            postalCode: '69001',
            country: 'France',
        );

        $this->assertTrue($address1->equals($address2));
        $this->assertFalse($address1->equals($address3));
    }

    public function testToString(): void
    {
        $address = new ShippingAddress(
            street: '123 Main Street',
            city: 'Paris',
            postalCode: '75001',
            country: 'France',
        );

        $this->assertSame('123 Main Street, 75001 Paris, France', (string) $address);
    }
}
