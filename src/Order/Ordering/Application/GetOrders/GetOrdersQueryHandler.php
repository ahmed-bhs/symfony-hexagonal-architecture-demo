<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\GetOrders;

use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Query Handler.
 *
 * Handles the execution of GetOrdersQuery.
 * Contains the read logic to fetch and return data.
 */
#[AsMessageHandler]
final readonly class GetOrdersQueryHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(GetOrdersQuery $query): GetOrdersResponse
    {
        // TODO: Implement your read logic here

        // Example:
        // $data = $this->repository->findById($query->id);
        //
        // return new GetOrdersResponse(
        //     id: $data->getId(),
        //     name: $data->getName(),
        //     email: $data->getEmail()
        // );

        throw new \RuntimeException('Query handler not yet implemented');
    }
}
