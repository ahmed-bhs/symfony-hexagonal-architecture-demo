<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProduct;

use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements QueryHandlerInterface<GetProductQuery, GetProductResponse>
 */
#[AsMessageHandler]
final readonly class GetProductQueryHandler implements QueryHandlerInterface
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
