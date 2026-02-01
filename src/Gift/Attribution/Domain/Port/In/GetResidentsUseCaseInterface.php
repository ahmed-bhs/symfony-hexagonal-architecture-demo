<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Port\In;

use App\Gift\Attribution\Application\Query\GetResidents\GetResidentsQuery;
use App\Gift\Attribution\Application\Query\GetResidents\GetResidentsResponse;

/**
 * Port IN (Primary/Driving) - Get Residents Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Get Residents" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface GetResidentsUseCaseInterface
{
    public function __invoke(GetResidentsQuery $query): GetResidentsResponse;
}
