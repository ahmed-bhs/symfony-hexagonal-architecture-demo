<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Exception;

class InvalidOrderStateException extends \DomainException
{
    public static function cannotTransition(string $orderId, string $from, string $to): self
    {
        return new self(sprintf(
            'Order "%s" cannot transition from "%s" to "%s"',
            $orderId,
            $from,
            $to,
        ));
    }

    public static function emptyCart(string $cartId): self
    {
        return new self(sprintf('Cannot place order from empty cart "%s"', $cartId));
    }
}
