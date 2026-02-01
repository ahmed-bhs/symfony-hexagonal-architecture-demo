<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine;

use App\Gift\Attribution\Domain\Model\GiftAttribution;
use App\Gift\Attribution\Domain\Port\Out\GiftAttributionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineGiftAttributionRepository implements GiftAttributionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(GiftAttribution $giftAttribution): void
    {
        $this->entityManager->persist($giftAttribution);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?GiftAttribution
    {
        return $this->entityManager->find(GiftAttribution::class, $id);
    }

    public function delete(GiftAttribution $giftAttribution): void
    {
        $this->entityManager->remove($giftAttribution);
        $this->entityManager->flush();
    }

    /**
     * @return GiftAttribution[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(GiftAttribution::class)->findAll();
    }
}
