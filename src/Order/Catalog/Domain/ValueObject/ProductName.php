<?php

declare(strict_types=1);

namespace App\Order\Catalog\Domain\ValueObject;

final readonly class ProductName
{
    public function __construct(
        public string $value,
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Product name cannot be empty');
        }

        if (strlen(trim($value)) < 2) {
            throw new \InvalidArgumentException('Product name must be at least 2 characters');
        }

        if (strlen(trim($value)) > 200) {
            throw new \InvalidArgumentException('Product name cannot exceed 200 characters');
        }
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
