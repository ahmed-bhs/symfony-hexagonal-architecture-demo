<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Adapter\Out\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Custom Constraint: Gift Available
 *
 * Validates that a Gift is available for attribution.
 * This is an Infrastructure concern that delegates to Domain logic.
 *
 * HEXAGONAL ARCHITECTURE:
 * - Infrastructure validator (uses Symfony Validator)
 * - Delegates to Domain logic (Gift::canBeAttributed())
 * - Provides early feedback (UI validation)
 *
 * Usage:
 * #[GiftAvailable]
 * public string $giftId;
 *
 * Note: This is a PRELIMINARY validation (fast feedback).
 * The FINAL validation happens in the Handler (atomic, in transaction).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class GiftAvailable extends Constraint
{
    public string $message = 'The gift "{{ name }}" is not available (out of stock)';
    public string $notFoundMessage = 'The gift with ID "{{ id }}" was not found';

    public function validatedBy(): string
    {
        return GiftAvailableValidator::class;
    }
}
