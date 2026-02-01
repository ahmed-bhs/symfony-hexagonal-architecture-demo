<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine;

use App\Gift\Attribution\Domain\Model\Resident;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use App\Shared\Pagination\Domain\ValueObject\Page;
use App\Shared\Pagination\Domain\ValueObject\PaginatedResult;
use App\Shared\Pagination\Domain\ValueObject\PerPage;
use App\Shared\Pagination\Domain\ValueObject\Total;
use App\Shared\Search\Domain\ValueObject\SearchTerm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineResidentRepository implements ResidentRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Resident $resident): void
    {
        $this->entityManager->persist($resident);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Resident
    {
        return $this->entityManager->find(Resident::class, $id);
    }

    public function delete(Resident $resident): void
    {
        $this->entityManager->remove($resident);
        $this->entityManager->flush();
    }

    /**
     * @return Resident[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Resident::class)->findAll();
    }

    public function findByEmail(string $email): ?Resident
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Resident::class, 'r')
            ->where('r.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByEmail(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findPaginated(Page $page, PerPage $perPage): PaginatedResult
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Resident::class, 'r')
            ->orderBy('r.name', 'ASC');

        return $this->paginate($qb, $page, $perPage);
    }

    public function searchPaginated(SearchTerm $searchTerm, Page $page, PerPage $perPage): PaginatedResult
    {
        if ($searchTerm->isEmpty()) {
            return $this->findPaginated($page, $perPage);
        }

        $qb = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Resident::class, 'r')
            ->where('r.name LIKE :search OR r.firstName LIKE :search OR r.email LIKE :search')
            ->setParameter('search', '%' . $searchTerm->value . '%')
            ->orderBy('r.name', 'ASC');

        return $this->paginate($qb, $page, $perPage);
    }

    private function paginate(QueryBuilder $qb, Page $page, PerPage $perPage): PaginatedResult
    {
        $qb->setFirstResult(($page->toInt() - 1) * $perPage->toInt())
            ->setMaxResults($perPage->toInt());

        $paginator = new Paginator($qb->getQuery());
        $total = new Total(count($paginator));

        return new PaginatedResult(
            items: iterator_to_array($paginator),
            currentPage: $page,
            perPage: $perPage,
            total: $total
        );
    }
}
