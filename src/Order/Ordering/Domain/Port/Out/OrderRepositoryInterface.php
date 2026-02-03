<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Port\Out;

use App\Order\Ordering\Domain\Model\Order;

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
interface OrderRepositoryInterface
{
    /**
     * Persist an entity to the storage.
     */
    public function save(Order $order): void;

    /**
     * Find an entity by its identifier.
     */
    public function find(string $id): ?Order;

    /**
     * Retrieve all entities.
     *
     * @return Order[]
     */
    public function findAll(): array;
}
