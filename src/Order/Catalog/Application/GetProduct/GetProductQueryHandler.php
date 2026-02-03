<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProduct;

use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Query Handler.
 *
 * Handles the execution of GetProductQuery.
 * Contains the read logic to fetch and return data.
 */
#[AsMessageHandler]
final readonly class GetProductQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(GetProductQuery $query): GetProductResponse
    {
        // TODO: Implement your read logic here

        // Example:
        // $data = $this->repository->findById($query->id);
        //
        // return new GetProductResponse(
        //     id: $data->getId(),
        //     name: $data->getName(),
        //     email: $data->getEmail()
        // );

        throw new \RuntimeException('Query handler not yet implemented');
    }
}
