<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Exception;

class CartNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Cart with ID "%s" was not found', $id));
    }

    public static function forCustomer(string $customerId): self
    {
        return new self(sprintf('No cart found for customer "%s"', $customerId));
    }
}
