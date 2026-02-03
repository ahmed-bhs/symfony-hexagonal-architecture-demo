<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Model;

use App\Order\Cart\Domain\Event\CartCleared;
use App\Order\Cart\Domain\Event\ItemAddedToCart;
use App\Order\Cart\Domain\Event\ItemRemovedFromCart;
use App\Order\Cart\Domain\Exception\CartItemNotFoundException;
use App\Shared\Domain\Aggregate\AggregateRoot;

class Cart
{
    use AggregateRoot;

    private string $id;
    private string $customerId;
    /** @var CartItem[] */
    private array $items = [];
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        string $id,
        string $customerId,
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(string $id, string $customerId): self
    {
        return new self($id, $customerId);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /** @return CartItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function addItem(
        string $itemId,
        string $productId,
        string $productName,
        int $unitPrice,
        int $quantity,
    ): void {
        foreach ($this->items as $item) {
            if ($item->isForProduct($productId)) {
                $item->increaseQuantity($quantity);
                $this->touch();
                $this->recordThat(new ItemAddedToCart(
                    $this->id,
                    $productId,
                    $productName,
                    $quantity,
                    $unitPrice,
                    new \DateTimeImmutable(),
                ));

                return;
            }
        }

        $this->items[] = CartItem::create($itemId, $productId, $productName, $unitPrice, $quantity);
        $this->touch();
        $this->recordThat(new ItemAddedToCart(
            $this->id,
            $productId,
            $productName,
            $quantity,
            $unitPrice,
            new \DateTimeImmutable(),
        ));
    }

    public function removeItem(string $productId): void
    {
        $found = false;

        $this->items = array_values(array_filter(
            $this->items,
            function (CartItem $item) use ($productId, &$found) {
                if ($item->isForProduct($productId)) {
                    $found = true;

                    return false;
                }

                return true;
            }
        ));

        if (!$found) {
            throw CartItemNotFoundException::forProduct($productId);
        }

        $this->touch();
        $this->recordThat(new ItemRemovedFromCart(
            $this->id,
            $productId,
            new \DateTimeImmutable(),
        ));
    }

    public function clear(): void
    {
        $this->items = [];
        $this->touch();
        $this->recordThat(new CartCleared(
            $this->id,
            new \DateTimeImmutable(),
        ));
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getSubtotal();
        }

        return $total;
    }

    public function getItemCount(): int
    {
        $count = 0;
        foreach ($this->items as $item) {
            $count += $item->getQuantity();
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function hasProduct(string $productId): bool
    {
        foreach ($this->items as $item) {
            if ($item->isForProduct($productId)) {
                return true;
            }
        }

        return false;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
