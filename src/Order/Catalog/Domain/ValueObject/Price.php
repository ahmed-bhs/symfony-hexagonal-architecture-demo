<?php

declare(strict_types=1);

namespace App\Order\Catalog\Domain\ValueObject;

final readonly class Price
{
    /**
     * @param int $amount Price in cents (e.g. 1999 = 19.99 EUR)
     */
    public function __construct(
        public int $amount,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
    }

    public function multiply(int $quantity): self
    {
        return new self($this->amount * $quantity);
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function format(): string
    {
        return number_format($this->amount / 100, 2, '.', ' ') . ' EUR';
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
