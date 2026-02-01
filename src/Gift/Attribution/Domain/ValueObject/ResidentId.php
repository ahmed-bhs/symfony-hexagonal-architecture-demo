<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\ValueObject;

/**
 * Domain Value Object.
 *
 * Represents a domain concept defined by its attributes rather than identity.
 * Value objects are immutable and can be compared by value.
 *
 * In hexagonal architecture, value objects are part of the Domain layer
 * and help enforce domain invariants and encapsulate business rules.
 *
 * HEXAGONAL: This ValueObject does NOT generate UUIDs itself.
 * The UUID is generated in the Application/Infrastructure layer and passed here.
 * This keeps the Domain pure without external dependencies.
 */
final readonly class ResidentId
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        public string $value,
    ) {
        // UUID format validation (no external dependency)
        if (!preg_match(self::UUID_PATTERN, $value)) {
            throw new \InvalidArgumentException(sprintf('Invalid UUID format: "%s"', $value));
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
