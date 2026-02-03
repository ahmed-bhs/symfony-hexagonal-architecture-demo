<?php

declare(strict_types=1);

namespace App\Order\Ordering\Infrastructure\Persistence\Doctrine;

use App\Order\Ordering\Domain\Model\Order;
use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function find(string $id): ?Order
    {
        return $this->entityManager->find(Order::class, $id);
    }

    /**
     * @return Order[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Order::class)->findAll();
    }
}
