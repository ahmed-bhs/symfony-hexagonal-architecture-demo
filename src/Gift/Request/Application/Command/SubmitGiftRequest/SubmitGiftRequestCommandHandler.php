<?php

declare(strict_types=1);

namespace App\Gift\Request\Application\Command\SubmitGiftRequest;

use App\Gift\Request\Domain\Model\GiftRequest;
use App\Gift\Request\Domain\Port\Out\GiftRequestRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Port\Out\IdGeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Command Handler for gift request submission.
 *
 * Pure hexagonal architecture:
 * - Depends only on domain ports (interfaces)
 * - Validation done by ValidationMiddleware before handler
 * - Domain events published automatically by infrastructure listener
 *
 * @implements CommandHandlerInterface<SubmitGiftRequestCommand, void>
 */
#[AsMessageHandler(bus: 'command.bus')]
final readonly class SubmitGiftRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private IdGeneratorInterface $idGenerator,
        private GiftRequestRepositoryInterface $repository,
    ) {
    }

    public function __invoke(SubmitGiftRequestCommand $command): void
    {
        $giftRequest = GiftRequest::create(
            id: $this->idGenerator->generate(),
            requesterName: $command->requesterName,
            requesterEmail: $command->requesterEmail,
            requesterPhone: $command->requesterPhone,
            requestedGift: $command->requestedGift,
            motivation: $command->motivation,
        );

        $this->repository->save($giftRequest);
    }
}
