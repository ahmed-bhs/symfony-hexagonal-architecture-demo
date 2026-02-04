<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\ConfirmOrder;

use App\Shared\Application\Command\CommandInterface;

final readonly class ConfirmOrderCommand implements CommandInterface
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
