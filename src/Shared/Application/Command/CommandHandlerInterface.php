<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

/**
 * Generic Command Handler Interface.
 *
 * All command handlers should implement this interface.
 * Commands represent intentions to change state in the system.
 *
 * Type safety is enforced via PHPDoc generics, not PHP signatures,
 * to allow concrete implementations with specific command types.
 *
 * @template TCommand of object
 * @template TResult
 */
interface CommandHandlerInterface
{
}
