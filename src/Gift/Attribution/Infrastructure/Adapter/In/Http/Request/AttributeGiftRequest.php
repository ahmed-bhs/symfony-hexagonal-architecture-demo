<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\In\Http\Request;

use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommand;
use App\Gift\Attribution\Domain\ValueObject\GiftId;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;
use App\Shared\Infrastructure\Adapter\Out\Validator\Constraint\GiftAvailable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Request DTO: Attribute Gift
 *
 * HEXAGONAL ARCHITECTURE - UI LAYER:
 * This Request DTO belongs to the UI layer (not Application).
 * It's responsible for HTTP validation and transformation to Application Command.
 *
 * Separation of concerns:
 * - UI Layer (this class): HTTP validation, format validation, custom validators
 * - Application Layer (Command): Pure PHP, no Symfony dependencies
 * - Domain Layer: Business rules, Value Objects
 *
 * Validation flow:
 * 1. Symfony validates this DTO (#[MapRequestPayload] in Controller)
 * 2. If valid -> toCommand() converts to pure Application Command
 * 3. Command dispatched to Handler (already validated)
 *
 * Benefits:
 * - Application Command stays 100% pure (no Symfony annotations)
 * - Custom validators (#[GiftAvailable]) stay in UI/Infrastructure
 * - Clear separation: UI validation vs Application logic
 * - Testable: Can test Command without HTTP concerns
 */
final readonly class AttributeGiftRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'The resident ID is required')]
        #[Assert\Uuid(message: 'The resident ID must be a valid UUID')]
        public string $residentId,

        #[Assert\NotBlank(message: 'The gift ID is required')]
        #[Assert\Uuid(message: 'The gift ID must be a valid UUID')]
        #[GiftAvailable]  // Custom validator (preliminary validation)
        public string $giftId,
    ) {}

    /**
     * Converts this Request DTO to a pure Application Command.
     *
     * UI -> Application boundary:
     * - Request DTO (UI): strings with validation annotations
     * - Command (Application): Value Objects, pure PHP
     *
     * @return AttributeGiftCommand Pure Application Command
     */
    public function toCommand(): AttributeGiftCommand
    {
        return new AttributeGiftCommand(
            residentId: new ResidentId($this->residentId),
            giftId: new GiftId($this->giftId)
        );
    }
}
