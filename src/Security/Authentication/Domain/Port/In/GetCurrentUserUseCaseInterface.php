<?php

declare(strict_types=1);

namespace App\Security\Authentication\Domain\Port\In;

use App\Security\Authentication\Application\Query\GetCurrentUser\GetCurrentUserQuery;
use App\Security\User\Application\DTO\UserDTO;

/**
 * Port IN (Primary/Driving) - Get Current User Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Get Current User" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface GetCurrentUserUseCaseInterface
{
    public function __invoke(GetCurrentUserQuery $query): ?UserDTO;
}
