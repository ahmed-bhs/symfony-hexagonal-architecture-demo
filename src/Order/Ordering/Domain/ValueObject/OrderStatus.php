<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\ValueObject;

final readonly class OrderStatus
{
    public const string PENDING = 'pending';
    public const string CONFIRMED = 'confirmed';
    public const string SHIPPED = 'shipped';
    public const string DELIVERED = 'delivered';
    public const string CANCELLED = 'cancelled';

    private const array VALID_STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
    ];

    private const array TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::SHIPPED, self::CANCELLED],
        self::SHIPPED => [self::DELIVERED],
        self::DELIVERED => [],
        self::CANCELLED => [],
    ];

    public function __construct(
        public string $value,
    ) {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid order status: "%s"', $value));
        }
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function confirmed(): self
    {
        return new self(self::CONFIRMED);
    }

    public static function shipped(): self
    {
        return new self(self::SHIPPED);
    }

    public static function delivered(): self
    {
        return new self(self::DELIVERED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next->value, self::TRANSITIONS[$this->value] ?? [], true);
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->value === self::CONFIRMED;
    }

    public function isShipped(): bool
    {
        return $this->value === self::SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->value === self::DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }

    public function isFinal(): bool
    {
        return $this->isDelivered() || $this->isCancelled();
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
