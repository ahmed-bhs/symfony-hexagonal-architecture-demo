<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\In;

use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommand;

/**
 * Port IN (Primary/Driving) - Attribute Gift Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Attribute Gift" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface AttributeGiftUseCaseInterface
{
    public function __invoke(AttributeGiftCommand $command): void;
}
