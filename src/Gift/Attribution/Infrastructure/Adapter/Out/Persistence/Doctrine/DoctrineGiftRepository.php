<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine;

use App\Gift\Attribution\Domain\Model\Gift;
use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineGiftRepository implements GiftRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Gift $gift): void
    {
        $this->entityManager->persist($gift);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Gift
    {
        return $this->entityManager->find(Gift::class, $id);
    }

    public function delete(Gift $gift): void
    {
        $this->entityManager->remove($gift);
        $this->entityManager->flush();
    }

    /**
     * @return Gift[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Gift::class)->findAll();
    }

    public function findByName(string $name): ?Gift
    {
        return $this->entityManager->getRepository(Gift::class)->findOneBy(['name' => $name]);
    }

    /**
     * @return Gift[]
     */
    public function findAllInStock(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('g')
            ->from(Gift::class, 'g')
            ->where('g.quantity > 0')
            ->getQuery()
            ->getResult();
    }
}
