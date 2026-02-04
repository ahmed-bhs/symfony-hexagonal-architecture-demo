<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\DeliverOrder;

use App\Shared\Application\Command\CommandInterface;

final readonly class DeliverOrderCommand implements CommandInterface
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
