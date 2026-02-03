<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Port\Out;

use App\Order\Cart\Domain\Model\Cart;

/**
 * Repository Port (Output/Driven Port).
 *
 * This is an OUTPUT port in hexagonal architecture - it defines
 * the contract for persistence operations. The application layer
 * uses this interface to interact with data storage without
 * coupling to any specific infrastructure implementation.
 *
 * Output ports are implemented by SECONDARY ADAPTERS (driven side)
 * in the Infrastructure layer (e.g., Doctrine repositories).
 *
 * @see https://alistair.cockburn.us/hexagonal-architecture/
 */
interface CartRepositoryInterface
{
    /**
     * Persist an entity to the storage.
     */
    public function save(Cart $cart): void;

    /**
     * Find an entity by its identifier.
     */
    public function find(string $id): ?Cart;

    /**
     * Retrieve all entities.
     *
     * @return Cart[]
     */
    public function findAll(): array;
}
