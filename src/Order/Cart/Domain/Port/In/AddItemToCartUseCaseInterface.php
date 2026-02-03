<?php

declare(strict_types=1);

namespace App\Order\Cart\Domain\Port\In;

/**
 * Use Case Port (Input/Driving Port).
 *
 * This is an INPUT port in hexagonal architecture - it defines
 * the contract for a use case that the application exposes.
 * Primary adapters (Controllers, CLI commands, etc.) use this
 * interface to drive the application.
 *
 * Input ports are implemented by USE CASES in the Application layer.
 * They define what the application CAN DO.
 *
 * @see https://alistair.cockburn.us/hexagonal-architecture/
 */
interface AddItemToCartUseCaseInterface
{
    /**
     * Execute the use case.
     */
    public function execute(): void;
}
