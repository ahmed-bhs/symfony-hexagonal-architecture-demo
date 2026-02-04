<?php

declare(strict_types=1);

namespace App\Order\Catalog\Application\CreateProduct;

use App\Shared\Application\Command\CommandInterface;

final readonly class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $description,
        public int $price,
        public int $stock,
    ) {
    }
}
