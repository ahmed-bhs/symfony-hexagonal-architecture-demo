<?php

declare(strict_types=1);

namespace App\Order\Catalog\Domain\Model;

use App\Order\Catalog\Domain\Exception\InsufficientStockException;
use App\Order\Catalog\Domain\ValueObject\Price;
use App\Order\Catalog\Domain\ValueObject\ProductName;

class Product
{
    private string $id;
    private ProductName $name;
    private string $description;
    private Price $price;
    private int $stock;

    private function __construct(
        string $id,
        ProductName $name,
        string $description,
        Price $price,
        int $stock,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = trim($description);
        $this->price = $price;

        if ($stock < 0) {
            throw new \InvalidArgumentException('Stock cannot be negative');
        }

        $this->stock = $stock;
    }

    public static function create(
        string $id,
        string $name,
        string $description,
        int $priceAmount,
        int $stock,
    ): self {
        return new self(
            $id,
            new ProductName($name),
            $description,
            new Price($priceAmount),
            $stock,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ProductName
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isAvailable(int $requestedQuantity): bool
    {
        return $this->stock >= $requestedQuantity;
    }

    public function decreaseStock(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        if (!$this->isAvailable($quantity)) {
            throw InsufficientStockException::forProduct(
                $this->id,
                (string) $this->name,
                $quantity,
                $this->stock,
            );
        }

        $this->stock -= $quantity;
    }

    public function increaseStock(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        $this->stock += $quantity;
    }
}
