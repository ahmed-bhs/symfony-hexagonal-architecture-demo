<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\GetOrders;

use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements QueryHandlerInterface<GetOrdersQuery, GetOrdersResponse>
 */
#[AsMessageHandler]
final readonly class GetOrdersQueryHandler implements QueryHandlerInterface
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
