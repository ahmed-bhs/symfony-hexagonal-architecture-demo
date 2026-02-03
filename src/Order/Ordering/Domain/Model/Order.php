<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Model;

use App\Order\Ordering\Domain\Event\OrderCancelled;
use App\Order\Ordering\Domain\Event\OrderConfirmed;
use App\Order\Ordering\Domain\Event\OrderDelivered;
use App\Order\Ordering\Domain\Event\OrderPlaced;
use App\Order\Ordering\Domain\Event\OrderShipped;
use App\Order\Ordering\Domain\Exception\InvalidOrderStateException;
use App\Order\Ordering\Domain\Exception\OrderAlreadyCancelledException;
use App\Order\Ordering\Domain\ValueObject\OrderStatus;
use App\Order\Ordering\Domain\ValueObject\OrderTotal;
use App\Order\Ordering\Domain\ValueObject\ShippingAddress;
use App\Shared\Domain\Aggregate\AggregateRoot;

class Order
{
    use AggregateRoot;

    private string $id;
    private string $customerId;
    private string $customerEmail;
    /** @var OrderItem[] */
    private array $items;
    private OrderTotal $totalAmount;
    private ShippingAddress $shippingAddress;
    private OrderStatus $status;
    private ?string $trackingNumber;
    private ?string $cancellationReason;
    private \DateTimeImmutable $createdAt;

    private function __construct(
        string $id,
        string $customerId,
        string $customerEmail,
        array $items,
        OrderTotal $totalAmount,
        ShippingAddress $shippingAddress,
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->customerEmail = $customerEmail;
        $this->items = $items;
        $this->totalAmount = $totalAmount;
        $this->shippingAddress = $shippingAddress;
        $this->status = OrderStatus::pending();
        $this->trackingNumber = null;
        $this->cancellationReason = null;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param OrderItem[] $items
     */
    public static function place(
        string $id,
        string $customerId,
        string $customerEmail,
        array $items,
        ShippingAddress $shippingAddress,
    ): self {
        if (empty($items)) {
            throw new \InvalidArgumentException('Order must have at least one item');
        }

        $total = 0;
        foreach ($items as $item) {
            $total += $item->getSubtotal();
        }

        $order = new self(
            $id,
            $customerId,
            $customerEmail,
            $items,
            new OrderTotal($total),
            $shippingAddress,
        );

        $order->recordThat(new OrderPlaced(
            $id,
            $customerEmail,
            $total,
            count($items),
            new \DateTimeImmutable(),
        ));

        return $order;
    }

    public function confirm(): void
    {
        $this->transitionTo(OrderStatus::confirmed());

        $this->recordThat(new OrderConfirmed(
            $this->id,
            new \DateTimeImmutable(),
        ));
    }

    public function ship(string $trackingNumber): void
    {
        if (empty(trim($trackingNumber))) {
            throw new \InvalidArgumentException('Tracking number cannot be empty');
        }

        $this->transitionTo(OrderStatus::shipped());
        $this->trackingNumber = $trackingNumber;

        $this->recordThat(new OrderShipped(
            $this->id,
            $trackingNumber,
            new \DateTimeImmutable(),
        ));
    }

    public function deliver(): void
    {
        $this->transitionTo(OrderStatus::delivered());

        $this->recordThat(new OrderDelivered(
            $this->id,
            new \DateTimeImmutable(),
        ));
    }

    public function cancel(string $reason): void
    {
        if ($this->status->isCancelled()) {
            throw OrderAlreadyCancelledException::withId($this->id);
        }

        $this->transitionTo(OrderStatus::cancelled());
        $this->cancellationReason = $reason;

        $this->recordThat(new OrderCancelled(
            $this->id,
            $reason,
            new \DateTimeImmutable(),
        ));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /** @return OrderItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalAmount(): OrderTotal
    {
        return $this->totalAmount;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getItemCount(): int
    {
        return count($this->items);
    }

    private function transitionTo(OrderStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw InvalidOrderStateException::cannotTransition(
                $this->id,
                $this->status->value,
                $newStatus->value,
            );
        }

        $this->status = $newStatus;
    }
}
