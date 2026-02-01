<?php

declare(strict_types=1);

namespace App\Security\User\Domain\Port\In;

use App\Security\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\Security\User\Domain\ValueObject\UserId;

/**
 * Port IN (Primary/Driving) - Register User Use Case.
 *
 * This is a driving port in hexagonal architecture - an interface that defines
 * the contract for the "Register User" use case.
 *
 * Ports IN represent the entry points into the application's core logic.
 * They are implemented by Application layer handlers and called by Adapters IN
 * (Controllers, CLI, Consumers).
 */
interface RegisterUserUseCaseInterface
{
    public function __invoke(RegisterUserCommand $command): UserId;
}
