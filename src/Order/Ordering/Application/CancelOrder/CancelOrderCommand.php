<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\CancelOrder;

use App\Shared\Application\Command\CommandInterface;

final readonly class CancelOrderCommand implements CommandInterface
{
    public function __construct(
        public string $orderId,
        public string $reason,
    ) {
    }
}
