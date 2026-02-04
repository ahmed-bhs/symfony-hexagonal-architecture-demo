<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\RemoveItemFromCart;

use App\Shared\Application\Command\CommandInterface;

final readonly class RemoveItemFromCartCommand implements CommandInterface
{
    public function __construct(
        public string $cartId,
        public string $productId,
    ) {
    }
}
