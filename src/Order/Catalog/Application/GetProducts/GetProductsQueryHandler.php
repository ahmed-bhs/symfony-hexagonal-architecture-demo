<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProducts;

use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Query Handler.
 *
 * Handles the execution of GetProductsQuery.
 * Contains the read logic to fetch and return data.
 */
#[AsMessageHandler]
final readonly class GetProductsQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function __invoke(GetProductsQuery $query): GetProductsResponse
    {
        // TODO: Implement your read logic here

        // Example:
        // $data = $this->repository->findById($query->id);
        //
        // return new GetProductsResponse(
        //     id: $data->getId(),
        //     name: $data->getName(),
        //     email: $data->getEmail()
        // );

        throw new \RuntimeException('Query handler not yet implemented');
    }
}
