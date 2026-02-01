<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Model;

use App\Gift\Attribution\Domain\Event\GiftAttributed;
use App\Shared\Domain\Aggregate\AggregateRoot;

/**
 * Aggregate Root: Gift Attribution.
 *
 * Represents the attribution of a gift to a resident.
 * This is an aggregate root that records domain events.
 *
 * Domain Events:
 * - GiftAttributed: When a gift is successfully attributed to a resident
 *
 * In hexagonal architecture, entities are part of the Domain layer (core)
 * and are completely independent of infrastructure concerns.
 *
 * IMPORTANT: This entity is PURE - no framework dependencies.
 * Doctrine ORM mapping is configured separately in XML.
 */
class GiftAttribution
{
    use AggregateRoot;
    private string $id;
    private string $residentId;
    private string $giftId;
    private \DateTimeImmutable $attributedAt;

    public function __construct(
        string $id,
        string $residentId,
        string $giftId,
        \DateTimeImmutable $attributedAt,
    ) {
        $this->id = $id;

        // Domain validation
        if (empty(trim($residentId))) {
            throw new \InvalidArgumentException('residentId cannot be empty');
        }

        if (empty(trim($giftId))) {
            throw new \InvalidArgumentException('giftId cannot be empty');
        }
        // Initialize properties
        $this->residentId = trim($residentId);
        $this->giftId = trim($giftId);
        $this->attributedAt = $attributedAt;
    }

    public static function createWithDetails(
        string $id,
        string $residentId,
        string $residentName,
        string $residentEmail,
        string $giftId,
        string $giftName
    ): self {
        $attribution = new self(
            $id,
            $residentId,
            $giftId,
            new \DateTimeImmutable()
        );

        // Record domain event - will be published by infrastructure after successful flush
        $attribution->recordThat(new GiftAttributed(
            attributionId: $attribution->id,
            residentId: $attribution->residentId,
            residentName: $residentName,
            residentEmail: $residentEmail,
            giftId: $attribution->giftId,
            giftName: $giftName,
            attributedAt: $attribution->attributedAt
        ));

        return $attribution;
    }

    /**
     * @deprecated Use createWithDetails() instead to ensure domain event has full context
     */
    public static function create(
        string $id,
        string $residentId,
        string $giftId
    ): self {
        return new self(
            $id,
            $residentId,
            $giftId,
            new \DateTimeImmutable()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    // Getters

    public function getResidentId(): string
    {
        return $this->residentId;
    }

    public function getGiftId(): string
    {
        return $this->giftId;
    }

    public function getAttributedAt(): \DateTimeImmutable
    {
        return $this->attributedAt;
    }
}
