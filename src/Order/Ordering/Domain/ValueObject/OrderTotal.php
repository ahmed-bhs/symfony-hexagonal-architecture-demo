<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\ValueObject;

final readonly class OrderTotal
{
    /**
     * @param int $amount Total in cents
     */
    public function __construct(
        public int $amount,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Order total cannot be negative');
        }
    }

    public function format(): string
    {
        return number_format($this->amount / 100, 2, '.', ' ') . ' EUR';
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
