<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Adapter\Out\Bus;

use App\Shared\Application\Query\QueryInterface;
use App\Shared\Domain\Port\Out\QueryBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerQueryBus implements QueryBusInterface
{
    public function __construct(
        private MessageBusInterface $queryBus,
    ) {
    }

    public function ask(QueryInterface $query): mixed
    {
        try {
            $envelope = $this->queryBus->dispatch($query);
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
