<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\Out;

use App\Gift\Attribution\Domain\Model\Resident;
use App\Shared\Pagination\Domain\ValueObject\Page;
use App\Shared\Pagination\Domain\ValueObject\PaginatedResult;
use App\Shared\Pagination\Domain\ValueObject\PerPage;
use App\Shared\Search\Domain\ValueObject\SearchTerm;

/**
 * Repository Port (Interface).
 *
 * This is a port in hexagonal architecture - an interface that defines
 * the contract for persistence operations without coupling to infrastructure.
 *
 * The application layer depends on this abstraction, not on concrete implementations.
 */
interface ResidentRepositoryInterface
{
    public function save(Resident $resident): void;

    public function findById(string $id): ?Resident;

    public function delete(Resident $resident): void;

    /**
     * @return Resident[]
     */
    public function findAll(): array;

    public function findByEmail(string $email): ?Resident;

    public function existsByEmail(string $email): bool;

    /**
     * Find residents with pagination.
     */
    public function findPaginated(Page $page, PerPage $perPage): PaginatedResult;

    /**
     * Search residents with pagination.
     */
    public function searchPaginated(SearchTerm $searchTerm, Page $page, PerPage $perPage): PaginatedResult;
}
