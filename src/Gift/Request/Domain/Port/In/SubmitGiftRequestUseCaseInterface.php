<?php

declare(strict_types=1);

namespace App\Gift\Request\Domain\Port\In;

use App\Gift\Request\Application\Command\SubmitGiftRequest\SubmitGiftRequestCommand;

/**
 * Port IN (Primary/Driving) - Submit Gift Request Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Submit Gift Request" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface SubmitGiftRequestUseCaseInterface
{
    public function __invoke(SubmitGiftRequestCommand $command): void;
}
