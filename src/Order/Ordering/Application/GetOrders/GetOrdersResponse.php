<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\GetOrders;

use App\Order\Ordering\Domain\Model\Order;

/**
 * Query Response.
 *
 * Contains the data returned by a query.
 * Should be immutable and contain only the data needed by the client.
 */
final readonly class GetOrdersResponse
{
    /**
     * @param Order[] $orders
     */
    public function __construct(
        public array $orders,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            fn (Order $order) => [
                'id' => $order->getId(),
                // TODO: Add other properties to expose
            ],
            $this->orders
        );
    }
}
