<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\CancelOrder;

use App\Order\Ordering\Domain\Exception\OrderNotFoundException;
use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CancelOrderCommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(CancelOrderCommand $command): void
    {
        $order = $this->orderRepository->find($command->orderId);
        if ($order === null) {
            throw OrderNotFoundException::withId($command->orderId);
        }

        $order->cancel($command->reason);

        $this->orderRepository->save($order);
    }
}
