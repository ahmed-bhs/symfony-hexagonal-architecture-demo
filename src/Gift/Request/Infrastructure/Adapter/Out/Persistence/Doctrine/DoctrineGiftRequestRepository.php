<?php

declare(strict_types=1);

namespace App\Gift\Request\Infrastructure\Adapter\Out\Persistence\Doctrine;

use App\Gift\Request\Domain\Model\GiftRequest;
use App\Gift\Request\Domain\Port\Out\GiftRequestRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineGiftRequestRepository implements GiftRequestRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(GiftRequest $giftRequest): void
    {
        $this->entityManager->persist($giftRequest);
        $this->entityManager->flush();
    }

    public function find(string $id): ?GiftRequest
    {
        return $this->entityManager->find(GiftRequest::class, $id);
    }

    /**
     * @return GiftRequest[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(GiftRequest::class)->findAll();
    }
}
