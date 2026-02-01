<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\DTO;

/**
 * DTO: Attribution Result.
 *
 * Data Transfer Object for transferring attribution results
 * from Application layer to UI layer (controllers, CLI).
 *
 * What is a DTO?
 * - Simple data container (no behavior)
 * - Transfers data between layers
 * - Can be serialized (JSON, XML)
 * - Immutable (readonly)
 *
 * DTO vs Domain Entity:
 * - DTO: Data transfer, serialization, UI concerns
 * - Entity: Business logic, invariants, identity
 *
 * DTO vs Value Object:
 * - DTO: Transport between layers
 * - VO: Domain concept with validation
 *
 * When to use DTO?
 * - API responses (REST, GraphQL)
 * - CLI command output
 * - Inter-service communication
 * - Complex query results (multiple aggregates)
 * - Not within same layer (use domain objects)
 * - Not simple CRUD (use domain entities directly)
 *
 * Benefits:
 * - Decouples UI from Domain
 * - Stable API contracts (domain can change)
 * - Optimized for serialization
 * - Can aggregate data from multiple sources
 */
final readonly class AttributionResultDTO
{
    public function __construct(
        public bool $success,
        public string $residentId,
        public string $residentName,
        public string $giftId,
        public string $giftName,
        public \DateTimeImmutable $attributedAt,
        public ?string $errorMessage = null
    ) {
    }

    /**
     * Create success DTO.
     */
    public static function success(
        string $residentId,
        string $residentName,
        string $giftId,
        string $giftName,
        \DateTimeImmutable $attributedAt
    ): self {
        return new self(
            success: true,
            residentId: $residentId,
            residentName: $residentName,
            giftId: $giftId,
            giftName: $giftName,
            attributedAt: $attributedAt
        );
    }

    /**
     * Create failure DTO.
     */
    public static function failure(
        string $residentId,
        string $reason,
        ?string $residentName = null,
        ?string $giftId = null,
        ?string $giftName = null
    ): self {
        return new self(
            success: false,
            residentId: $residentId,
            residentName: $residentName ?? 'Unknown',
            giftId: $giftId ?? '',
            giftName: $giftName ?? '',
            attributedAt: new \DateTimeImmutable(),
            errorMessage: $reason
        );
    }

    /**
     * Convert to array for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'resident' => [
                'id' => $this->residentId,
                'name' => $this->residentName,
            ],
            'gift' => [
                'id' => $this->giftId,
                'name' => $this->giftName,
            ],
            'attributedAt' => $this->attributedAt->format(\DateTimeInterface::ATOM),
            'error' => $this->errorMessage,
        ];
    }

    /**
     * Convert to JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
