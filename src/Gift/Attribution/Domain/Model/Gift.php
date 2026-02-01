<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Model;

/**
 * Domain Entity.
 *
 * Represents a domain concept with identity and lifecycle.
 * Contains business logic and enforces invariants.
 *
 * In hexagonal architecture, entities are part of the Domain layer (core)
 * and are completely independent of infrastructure concerns.
 *
 * IMPORTANT: This entity is PURE - no framework dependencies.
 * Doctrine ORM mapping is configured separately in:
 * Infrastructure/Persistence/Doctrine/Orm/Mapping/Gift.orm.xml
 */
class Gift
{
    private string $id;
    private string $name;
    private string $description;
    private int $quantity;

    private function __construct(
        string $id,
        string $name,
        string $description,
        int $quantity,
    ) {
        $this->id = $id;

        // Domain validation
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('name cannot be empty');
        }

        if (strlen(trim($name)) < 3) {
            throw new \InvalidArgumentException('name must be at least 3 characters');
        }

        if (strlen(trim($name)) > 100) {
            throw new \InvalidArgumentException('name cannot exceed 100 characters');
        }

        if ($quantity < 0) {
            throw new \InvalidArgumentException('quantity cannot be negative');
        }

        if ($quantity > 1000) {
            throw new \InvalidArgumentException('quantity cannot exceed 1000');
        }
        // Initialize properties
        $this->name = trim($name);
        $this->description = trim($description);
        $this->quantity = $quantity;
    }

    public static function create(
        string $id,
        string $name,
        string $description,
        int $quantity
    ): self {
        return new self(
            $id,
            $name,
            $description,
            $quantity
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    // Getters

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    // Business logic methods

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function isAvailable(int $requestedQuantity): bool
    {
        return $this->quantity >= $requestedQuantity;
    }

    /**
     * Business Rule: A gift can be attributed if stock is available.
     *
     * SINGLE SOURCE OF TRUTH for this business rule.
     * This method is called by:
     * - GiftAvailableValidator (Infrastructure - preliminary validation)
     * - AttributeGiftCommandHandler (Application - final validation)
     *
     * @return bool True if the gift can be attributed (stock > 0)
     */
    public function canBeAttributed(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Business Rule: Decrease stock atomically with validation.
     *
     * ATOMIC OPERATION: Validates and decreases stock in one method.
     * This protects against race conditions when called inside a transaction.
     *
     * Flow:
     * 1. Check if gift is available (canBeAttributed)
     * 2. If not -> throw exception (rollback transaction)
     * 3. If yes -> decrease stock
     *
     * @throws \DomainException If stock is insufficient
     */
    public function decreaseStock(): void
    {
        if (!$this->canBeAttributed()) {
            throw new \DomainException(
                sprintf('Cannot attribute gift "%s" - out of stock', $this->name)
            );
        }

        $this->quantity--;
    }
}
