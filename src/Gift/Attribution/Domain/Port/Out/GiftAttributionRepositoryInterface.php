<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\Out;

use App\Gift\Attribution\Domain\Model\GiftAttribution;

/**
 * Repository Port (Interface).
 *
 * This is a port in hexagonal architecture - an interface that defines
 * the contract for persistence operations without coupling to infrastructure.
 *
 * The application layer depends on this abstraction, not on concrete implementations.
 */
interface GiftAttributionRepositoryInterface
{
    public function save(GiftAttribution $attribution): void;

    public function findById(string $id): ?GiftAttribution;

    public function delete(GiftAttribution $attribution): void;

    /**
     * @return GiftAttribution[]
     */
    public function findAll(): array;
}
