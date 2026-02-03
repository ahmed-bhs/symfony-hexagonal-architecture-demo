<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\ValueObject;

final readonly class Quantity
{
    public function __construct(
        public int $value,
    ) {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }

        if ($value > 99) {
            throw new \InvalidArgumentException('Quantity cannot exceed 99');
        }
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
