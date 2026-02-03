<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Exception;

class OrderAlreadyCancelledException extends \DomainException
{
    public static function withId(string $orderId): self
    {
        return new self(sprintf('Order "%s" is already cancelled', $orderId));
    }
}
