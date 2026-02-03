<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\ConfirmOrder;

/**
 * CQRS Command.
 *
 * Represents an intention to perform a write operation.
 * Commands should be immutable and contain all the data needed to execute the action.
 */
final readonly class ConfirmOrderCommand
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
