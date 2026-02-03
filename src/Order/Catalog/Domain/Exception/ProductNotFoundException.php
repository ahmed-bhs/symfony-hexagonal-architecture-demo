<?php

declare(strict_types=1);

namespace App\Order\Catalog\Domain\Exception;

class ProductNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Product with ID "%s" was not found', $id));
    }
}
