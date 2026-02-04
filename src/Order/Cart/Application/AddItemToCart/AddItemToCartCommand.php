<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\AddItemToCart;

use App\Shared\Application\Command\CommandInterface;

final readonly class AddItemToCartCommand implements CommandInterface
{
    public function __construct(
        public string $cartId,
        public string $productId,
        public int $quantity,
    ) {
    }
}
