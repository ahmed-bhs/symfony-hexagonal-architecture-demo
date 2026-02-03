<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Exception;

class CartItemNotFoundException extends \DomainException
{
    public static function forProduct(string $productId): self
    {
        return new self(sprintf('Cart item for product "%s" was not found', $productId));
    }
}
