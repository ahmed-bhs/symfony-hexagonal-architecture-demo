<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\GetOrder;

/**
 * Query Response.
 *
 * Contains the data returned by a query.
 * Should be immutable and contain only the data needed by the client.
 */
final readonly class GetOrderResponse
{
    public function __construct(
        public Order $order,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->order->getId(),
            // TODO: Add other properties to expose
        ];
    }
}
