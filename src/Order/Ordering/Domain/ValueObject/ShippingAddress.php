<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\ValueObject;

final readonly class ShippingAddress
{
    public function __construct(
        public string $street,
        public string $city,
        public string $postalCode,
        public string $country,
    ) {
        if (empty(trim($street))) {
            throw new \InvalidArgumentException('Street cannot be empty');
        }

        if (empty(trim($city))) {
            throw new \InvalidArgumentException('City cannot be empty');
        }

        if (empty(trim($postalCode))) {
            throw new \InvalidArgumentException('Postal code cannot be empty');
        }

        if (empty(trim($country))) {
            throw new \InvalidArgumentException('Country cannot be empty');
        }
    }

    public function fullAddress(): string
    {
        return sprintf('%s, %s %s, %s', $this->street, $this->postalCode, $this->city, $this->country);
    }

    public function equals(self $other): bool
    {
        return $this->street === $other->street
            && $this->city === $other->city
            && $this->postalCode === $other->postalCode
            && $this->country === $other->country;
    }

    public function __toString(): string
    {
        return $this->fullAddress();
    }
}
