<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Exception;

class OrderNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Order with ID "%s" was not found', $id));
    }
}
