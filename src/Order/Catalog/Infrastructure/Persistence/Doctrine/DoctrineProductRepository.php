<?php

declare(strict_types=1);

namespace App\Order\Catalog\Infrastructure\Persistence\Doctrine;

use App\Order\Catalog\Domain\Model\Product;
use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository Adapter (Infrastructure).
 *
 * This is an adapter in hexagonal architecture - it implements the port interface
 * and provides the actual infrastructure implementation (Doctrine ORM in this case).
 *
 * This adapter translates domain operations to infrastructure-specific operations.
 */
final class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function find(string $id): ?Product
    {
        return $this->entityManager->find(Product::class, $id);
    }

    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }
}
