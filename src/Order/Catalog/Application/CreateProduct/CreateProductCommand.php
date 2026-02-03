<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\CreateProduct;

/**
 * CQRS Command.
 *
 * Represents an intention to perform a write operation.
 * Commands should be immutable and contain all the data needed to execute the action.
 */
final readonly class CreateProductCommand
{
    public function __construct(
        public string $name,
        public string $description,
        public int $price,
        public int $stock,
    ) {
    }
}
