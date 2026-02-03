<?php

declare(strict_types=1);

namespace App\Order\Ordering\Domain\Model;

class OrderItem
{
    private string $id;
    private string $productId;
    private string $productName;
    private int $unitPrice;
    private int $quantity;
    private ?Order $order = null;

    private function __construct(
        string $id,
        string $productId,
        string $productName,
        int $unitPrice,
        int $quantity,
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        $this->quantity = $quantity;
    }

    public static function create(
        string $id,
        string $productId,
        string $productName,
        int $unitPrice,
        int $quantity,
    ): self {
        return new self($id, $productId, $productName, $unitPrice, $quantity);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSubtotal(): int
    {
        return $this->unitPrice * $this->quantity;
    }
}
