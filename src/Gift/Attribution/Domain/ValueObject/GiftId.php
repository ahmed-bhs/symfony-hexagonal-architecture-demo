<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

/**
 * Gift ID Value Object
 *
 * Represents a unique identifier for a Gift.
 * Self-validating at construction.
 */
final readonly class GiftId
{
    public function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new \InvalidArgumentException('Gift ID cannot be empty');
        }

        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid Gift ID format: %s (expected UUID)', $value)
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
