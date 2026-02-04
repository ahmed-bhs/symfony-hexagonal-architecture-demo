<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Adapter\Out\Bus;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\Port\Out\CommandBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerCommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatch(CommandInterface $command): mixed
    {
        try {
            $envelope = $this->commandBus->dispatch($command);
            $stamp = $envelope->last(HandledStamp::class);

            return $stamp?->getResult();
        } catch (HandlerFailedException $e) {
            $exceptions = $e->getWrappedExceptions();
            if (1 === \count($exceptions)) {
                throw reset($exceptions);
            }

            throw $e;
        }
    }
}
