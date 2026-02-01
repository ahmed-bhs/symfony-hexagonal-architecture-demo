<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\In;

use App\Gift\Attribution\Application\Query\GetGifts\GetGiftsQuery;
use App\Gift\Attribution\Application\Query\GetGifts\GetGiftsResponse;

/**
 * Port IN (Primary/Driving) - Get Gifts Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Get Gifts" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface GetGiftsUseCaseInterface
{
    public function __invoke(GetGiftsQuery $query): GetGiftsResponse;
}
