<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\GetProduct;

/**
 * CQRS Query.
 *
 * Represents an intention to retrieve data (read operation).
 * Queries should be immutable and contain all parameters needed to fetch the data.
 */
final readonly class GetProductQuery
{
    public function __construct(
        // TODO: Add your query parameters here
        // Example:
        // public string $id,
    ) {
    }
}
