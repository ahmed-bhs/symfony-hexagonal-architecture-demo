<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProducts;

use App\Order\Catalog\Domain\Model\Product;

/**
 * Query Response.
 *
 * Contains the data returned by a query.
 * Should be immutable and contain only the data needed by the client.
 */
final readonly class GetProductsResponse
{
    /**
     * @param Product[] $products
     */
    public function __construct(
        public array $products,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            fn (Product $product) => [
                'id' => $product->getId(),
                // TODO: Add other properties to expose
            ],
            $this->products
        );
    }
}
