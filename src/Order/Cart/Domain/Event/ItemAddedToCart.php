<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;

final readonly class ItemAddedToCart implements DomainEvent
{
    public function __construct(
        public string $cartId,
        public string $productId,
        public string $productName,
        public int $quantity,
        public int $unitPrice,
        public \DateTimeImmutable $occurredAt,
    ) {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function aggregateId(): string
    {
        return $this->cartId;
    }
}
