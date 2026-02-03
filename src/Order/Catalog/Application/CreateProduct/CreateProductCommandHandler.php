<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\CreateProduct;

use App\Order\Catalog\Domain\Model\Product;
use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use App\Shared\Domain\Port\Out\IdGeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private IdGeneratorInterface $idGenerator,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = Product::create(
            $this->idGenerator->generate(),
            $command->name,
            $command->description,
            $command->price,
            $command->stock,
        );

        $this->productRepository->save($product);
    }
}
