<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

final readonly class OrderId
{
    public function __construct(
        private string $value,
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException('Order ID cannot be empty');
        }

        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid Order ID format: %s (expected UUID)', $value)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
