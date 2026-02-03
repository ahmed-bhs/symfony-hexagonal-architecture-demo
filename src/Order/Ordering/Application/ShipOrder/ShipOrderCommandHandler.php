<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\ShipOrder;

use App\Order\Ordering\Domain\Exception\OrderNotFoundException;
use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ShipOrderCommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ShipOrderCommand $command): void
    {
        $order = $this->orderRepository->find($command->orderId);
        if ($order === null) {
            throw OrderNotFoundException::withId($command->orderId);
        }

        $order->ship($command->trackingNumber);

        $this->orderRepository->save($order);
    }
}
