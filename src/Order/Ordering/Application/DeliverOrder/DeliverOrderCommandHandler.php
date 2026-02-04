<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\DeliverOrder;

use App\Order\Ordering\Domain\Exception\OrderNotFoundException;
use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandlerInterface<DeliverOrderCommand, void>
 */
#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeliverOrderCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(DeliverOrderCommand $command): void
    {
        $order = $this->orderRepository->find($command->orderId);
        if ($order === null) {
            throw OrderNotFoundException::withId($command->orderId);
        }

        $order->deliver();

        $this->orderRepository->save($order);
    }
}
