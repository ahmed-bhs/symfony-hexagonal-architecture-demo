<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\GetCart;

/**
 * CQRS Query.
 *
 * Represents an intention to retrieve data (read operation).
 * Queries should be immutable and contain all parameters needed to fetch the data.
 */
final readonly class GetCartQuery
{
    public function __construct(
        // TODO: Add your query parameters here
        // Example:
        // public string $id,
    ) {
    }
}
