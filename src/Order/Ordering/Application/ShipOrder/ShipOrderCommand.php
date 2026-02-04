<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\ShipOrder;

use App\Shared\Application\Command\CommandInterface;

final readonly class ShipOrderCommand implements CommandInterface
{
    public function __construct(
        public string $orderId,
        public string $trackingNumber,
    ) {
    }
}
