<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\GetCart;

/**
 * Query Response.
 *
 * Contains the data returned by a query.
 * Should be immutable and contain only the data needed by the client.
 */
final readonly class GetCartResponse
{
    public function __construct(
        public string $cartId,
        public string $items,
        public int $totalAmount,
        public int $itemCount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'cartId' => $this->cartId,
            'items' => $this->items,
            'totalAmount' => $this->totalAmount,
            'itemCount' => $this->itemCount,
        ];
    }
}
