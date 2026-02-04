<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\GetOrder;

use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements QueryHandlerInterface<GetOrderQuery, GetOrderResponse>
 */
#[AsMessageHandler]
final readonly class GetOrderQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(GetOrderQuery $query): GetOrderResponse
    {
        // TODO: Implement your read logic here

        // Example:
        // $data = $this->repository->findById($query->id);
        //
        // return new GetOrderResponse(
        //     id: $data->getId(),
        //     name: $data->getName(),
        //     email: $data->getEmail()
        // );

        throw new \RuntimeException('Query handler not yet implemented');
    }
}
