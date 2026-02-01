<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Service;

use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommand;
use App\Gift\Attribution\Application\DTO\AttributionResultDTO;
use App\Gift\Attribution\Application\Exception\NoEligibleGiftException;
use App\Gift\Attribution\Application\Query\GetGifts\GetGiftsQuery;
use App\Gift\Attribution\Application\Query\GetGifts\GetGiftsResponse;
use App\Gift\Attribution\Domain\Port\Out\GiftAttributionRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use App\Gift\Attribution\Domain\ValueObject\GiftId;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Application Service: Automatic Gift Attribution.
 *
 * This is an Application Service (NOT a Domain Service).
 * It orchestrates multiple use cases to achieve a complex workflow.
 *
 * What is an Application Service?
 * - Orchestrates Commands and Queries
 * - Coordinates multiple aggregates
 * - Contains NO business logic (delegates to Domain)
 * - Manages transactions and use case flow
 * - Can use multiple repositories (read-only for queries)
 *
 * When to use Application Service?
 * - Complex workflow across multiple use cases
 * - Need to query before executing command
 * - Orchestration logic (not business logic)
 * - Not simple CRUD (use Command/Query directly)
 * - Not business rules (belongs in Domain)
 *
 * Example use case:
 * "Automatically attribute the best available gift to a resident
 * based on their age, previous attributions, and available stock"
 *
 * Flow:
 * 1. Query available gifts
 * 2. Query resident's attribution history
 * 3. Apply business rules (via Domain)
 * 4. Select best match
 * 5. Execute attribution command
 * 6. Return result
 */
final readonly class AutomaticGiftAttributionService
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private ResidentRepositoryInterface $residentRepository,
        private GiftAttributionRepositoryInterface $attributionRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Automatically attribute a gift to a resident.
     *
     * This method orchestrates multiple steps:
     * - Query available gifts
     * - Check resident eligibility
     * - Select best match
     * - Execute attribution
     *
     * @throws NoEligibleGiftException If no suitable gift found
     * @throws \InvalidArgumentException If resident not found
     */
    public function attributeBestGift(string $residentId): AttributionResultDTO
    {
        $this->logger->info('Starting automatic gift attribution', [
            'residentId' => $residentId,
        ]);

        // Step 1: Validate resident exists
        $resident = $this->residentRepository->findById($residentId);
        if (!$resident) {
            throw new \InvalidArgumentException(
                sprintf('Resident with ID "%s" not found', $residentId)
            );
        }

        // Step 2: Query all available gifts
        $query = new GetGiftsQuery();

        $envelope = $this->queryBus->dispatch($query);
        /** @var GetGiftsResponse $response */
        $response = $envelope->last(HandledStamp::class)?->getResult();

        if (empty($response->gifts)) {
            throw new NoEligibleGiftException('No gifts available in stock');
        }

        // Step 3: Check attribution history (business rule: max 3 per year)
        $attributionsThisYear = $this->attributionRepository
            ->countForResidentThisYear($residentId);

        if ($attributionsThisYear >= 3) {
            throw new NoEligibleGiftException(
                'Resident has already received maximum gifts this year (3)'
            );
        }

        // Step 4: Select best match based on age and availability
        // This is orchestration logic, NOT business logic
        // Business rules are in Domain (Resident, Gift)
        $bestGift = $this->selectBestGiftForResident($resident, $response->gifts);

        if (!$bestGift) {
            throw new NoEligibleGiftException(
                'No eligible gift found for this resident'
            );
        }

        // Step 5: Execute attribution command
        $command = new AttributeGiftCommand(
            residentId: new ResidentId($residentId),
            giftId: new GiftId($bestGift['id'])
        );

        $this->commandBus->dispatch($command);

        $this->logger->info('Automatic gift attribution completed', [
            'residentId' => $residentId,
            'giftId' => $bestGift['id'],
            'giftName' => $bestGift['name'],
        ]);

        // Step 6: Return result DTO
        return new AttributionResultDTO(
            success: true,
            residentId: $residentId,
            residentName: $resident->getFullName(),
            giftId: $bestGift['id'],
            giftName: $bestGift['name'],
            attributedAt: new \DateTimeImmutable()
        );
    }

    /**
     * Select best gift for resident based on age and availability.
     *
     * This is ORCHESTRATION logic (Application concern).
     * Business rules like "isAdult()" belong in Domain.
     *
     * @param array<array{id: string, name: string, quantity: int}> $availableGifts
     * @return array{id: string, name: string, quantity: int}|null
     */
    private function selectBestGiftForResident(
        \App\Gift\Attribution\Domain\Model\Resident $resident,
        array $availableGifts
    ): ?array {
        // Filter gifts with stock > 0
        $inStock = array_filter(
            $availableGifts,
            fn(array $gift) => $gift['quantity'] > 0
        );

        if (empty($inStock)) {
            return null;
        }

        // Business rule delegation: Check age eligibility
        // (In real app, this would be in Domain Specification)
        $age = $resident->getAge();

        if ($age->isChild()) {
            // Prefer toys for children
            foreach ($inStock as $gift) {
                if (str_contains(strtolower($gift['name']), 'toy')) {
                    return $gift;
                }
            }
        } elseif ($age->isSenior()) {
            // Prefer books for seniors
            foreach ($inStock as $gift) {
                if (str_contains(strtolower($gift['name']), 'book')) {
                    return $gift;
                }
            }
        }

        // Default: return first available gift
        return reset($inStock) ?: null;
    }

    /**
     * Bulk attribution for multiple residents.
     *
     * Example of batch orchestration.
     *
     * @param string[] $residentIds
     * @return AttributionResultDTO[]
     */
    public function attributeGiftsInBulk(array $residentIds): array
    {
        $results = [];

        foreach ($residentIds as $residentId) {
            try {
                $results[] = $this->attributeBestGift($residentId);
            } catch (NoEligibleGiftException|\InvalidArgumentException $e) {
                $this->logger->warning('Failed to attribute gift in bulk', [
                    'residentId' => $residentId,
                    'error' => $e->getMessage(),
                ]);

                $results[] = AttributionResultDTO::failure(
                    residentId: $residentId,
                    reason: $e->getMessage()
                );
            }
        }

        return $results;
    }
}
