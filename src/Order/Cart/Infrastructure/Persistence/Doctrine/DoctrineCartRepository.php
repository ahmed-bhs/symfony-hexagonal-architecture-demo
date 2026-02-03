<?php

declare(strict_types=1);

namespace App\Order\Cart\Infrastructure\Persistence\Doctrine;

use App\Order\Cart\Domain\Model\Cart;
use App\Order\Cart\Domain\Port\Out\CartRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineCartRepository implements CartRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Cart $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function find(string $id): ?Cart
    {
        return $this->entityManager->find(Cart::class, $id);
    }

    /**
     * @return Cart[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Cart::class)->findAll();
    }
}
