<?php

declare(strict_types=1);

namespace App\Order\Catalog\Domain\Exception;

class InsufficientStockException extends \DomainException
{
    public static function forProduct(
        string $productId,
        string $productName,
        int $requested,
        int $available,
    ): self {
        return new self(sprintf(
            'Insufficient stock for product "%s" (ID: %s): requested %d, available %d',
            $productName,
            $productId,
            $requested,
            $available,
        ));
    }
}
