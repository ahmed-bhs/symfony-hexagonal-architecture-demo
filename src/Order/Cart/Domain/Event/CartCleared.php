<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;

final readonly class CartCleared implements DomainEvent
{
    public function __construct(
        public string $cartId,
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
