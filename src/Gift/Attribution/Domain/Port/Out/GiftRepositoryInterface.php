<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\Out;

use App\Gift\Attribution\Domain\Model\Gift;

/**
 * Repository Port (Interface).
 *
 * This is a port in hexagonal architecture - an interface that defines
 * the contract for persistence operations without coupling to infrastructure.
 *
 * The application layer depends on this abstraction, not on concrete implementations.
 */
interface GiftRepositoryInterface
{
    public function save(Gift $gift): void;

    public function findById(string $id): ?Gift;

    public function delete(Gift $gift): void;

    /**
     * @return Gift[]
     */
    public function findAll(): array;

    public function findByName(string $name): ?Gift;

    /**
     * @return Gift[]
     */
    public function findAllInStock(): array;
}
