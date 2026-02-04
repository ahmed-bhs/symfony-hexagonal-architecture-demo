<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Command\AttributeGift;

use App\Gift\Attribution\Domain\Model\GiftAttribution;
use App\Gift\Attribution\Domain\Port\Out\GiftAttributionRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Port\Out\IdGeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Command Handler for gift attribution.
 *
 * Pure hexagonal architecture:
 * - No Symfony dependency (except AsMessageHandler attribute for auto-wiring)
 * - Depends only on domain ports (interfaces)
 * - Can be tested with in-memory repositories
 *
 * Flow:
 * 1. Validate resident exists
 * 2. Validate gift exists and is available
 * 3. Decrease stock atomically (throws if unavailable)
 * 4. Create attribution with full context (for domain event)
 * 5. Persist both gift (updated stock) and attribution
 * 6. Domain events published automatically by infrastructure listener
 *
 * @implements CommandHandlerInterface<AttributeGiftCommand, void>
 */
#[AsMessageHandler(bus: 'command.bus')]
final readonly class AttributeGiftCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private IdGeneratorInterface $idGenerator,
        private ResidentRepositoryInterface $residentRepository,
        private GiftRepositoryInterface $giftRepository,
        private GiftAttributionRepositoryInterface $attributionRepository,
    ) {
    }

    public function __invoke(AttributeGiftCommand $command): void
    {
        // 1. Validate resident exists
        $resident = $this->residentRepository->findById($command->residentId->value);
        if ($resident === null) {
            throw new \InvalidArgumentException(
                sprintf('Resident with ID "%s" not found', $command->residentId->value)
            );
        }

        // 2. Validate gift exists
        $gift = $this->giftRepository->findById($command->giftId->value());
        if ($gift === null) {
            throw new \InvalidArgumentException(
                sprintf('Gift with ID "%s" not found', $command->giftId->value())
            );
        }

        // 3. Decrease stock atomically (throws DomainException if unavailable)
        $gift->decreaseStock();

        // 4. Create attribution with full context
        $attribution = GiftAttribution::createWithDetails(
            id: $this->idGenerator->generate(),
            residentId: $command->residentId->value,
            residentName: $resident->getFullName(),
            residentEmail: $resident->getEmail()->value,
            giftId: $command->giftId->value(),
            giftName: $gift->getName()
        );

        // 5. Persist
        $this->giftRepository->save($gift);
        $this->attributionRepository->save($attribution);
    }
}
