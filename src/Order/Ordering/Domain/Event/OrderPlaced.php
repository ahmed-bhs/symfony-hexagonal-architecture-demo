<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;

final readonly class OrderPlaced implements DomainEvent
{
    public function __construct(
        public string $orderId,
        public string $customerEmail,
        public int $totalAmount,
        public int $itemCount,
        public \DateTimeImmutable $occurredAt,
    ) {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function aggregateId(): string
    {
        return $this->orderId;
    }
}
