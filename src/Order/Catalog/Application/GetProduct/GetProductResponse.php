<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProduct;

/**
 * Query Response.
 *
 * Contains the data returned by a query.
 * Should be immutable and contain only the data needed by the client.
 */
final readonly class GetProductResponse
{
    public function __construct(
        public Product $product,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->product->getId(),
            // TODO: Add other properties to expose
        ];
    }
}
