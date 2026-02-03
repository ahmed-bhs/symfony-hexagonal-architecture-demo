<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\Message;

/**
 * Async Message (DTO).
 *
 * Message dispatched to Symfony Messenger for async processing.
 * Represents an intention to perform work asynchronously.
 *
 * Key differences from Commands:
 * - Commands: Synchronous, immediate execution
 * - Messages: Asynchronous, queued execution (workers)
 */
final readonly class SendOrderConfirmationMessage
{
    public function __construct(
        // Add your message properties here
        // public string $id,
        // public string $email,
        // public \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {
    }
}
