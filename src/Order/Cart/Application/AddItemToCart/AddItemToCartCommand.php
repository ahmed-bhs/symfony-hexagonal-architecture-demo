<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\AddItemToCart;

/**
 * CQRS Command.
 *
 * Represents an intention to perform a write operation.
 * Commands should be immutable and contain all the data needed to execute the action.
 */
final readonly class AddItemToCartCommand
{
    public function __construct(
        public string $cartId,
        public string $productId,
        public int $quantity,
    ) {
    }
}
