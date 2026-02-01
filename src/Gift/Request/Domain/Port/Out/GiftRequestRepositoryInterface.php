<?php

declare(strict_types=1);

namespace App\Gift\Request\Domain\Port\Out;

use App\Gift\Request\Domain\Model\GiftRequest;

/**
 * Repository Port (Interface).
 *
 * This is a port in hexagonal architecture - an interface that defines
 * the contract for persistence operations without coupling to infrastructure.
 *
 * The application layer depends on this abstraction, not on concrete implementations.
 */
interface GiftRequestRepositoryInterface
{
    /**
     * Persist an entity to the storage.
     */
    public function save(GiftRequest $giftRequest): void;

    /**
     * Find an entity by its identifier.
     */
    public function find(string $id): ?GiftRequest;

    /**
     * Retrieve all entities.
     *
     * @return GiftRequest[]
     */
    public function findAll(): array;
}
