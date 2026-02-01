<?php

declare(strict_types=1);

namespace App\Security\Authentication\Domain\Port\In;

use App\Security\Authentication\Application\Command\Login\LoginCommand;
use App\Security\Authentication\Application\DTO\TokenDTO;

/**
 * Port IN (Primary/Driving) - Login Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Login" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface LoginUseCaseInterface
{
    public function __invoke(LoginCommand $command): TokenDTO;
}
