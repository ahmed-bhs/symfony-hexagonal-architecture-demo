<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Adapter\Out\Validator\Constraint;

use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use App\Gift\Attribution\Domain\ValueObject\GiftId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Custom Validator: Gift Available
 *
 * Validates that a Gift is available for attribution.
 *
 * HEXAGONAL ARCHITECTURE - NO DUPLICATION:
 * This validator DELEGATES to Domain logic instead of duplicating the business rule.
 *
 * Flow:
 * 1. Load Gift entity from repository (Infrastructure)
 * 2. Call Gift::canBeAttributed() (Domain)
 * 3. If false -> validation error
 *
 * Benefits:
 * - Business rule defined ONCE in Domain (Gift::canBeAttributed())
 * - No duplication (validator uses Domain method)
 * - Early feedback (fast validation before Handler)
 * - Infrastructure concern (uses Symfony Validator + Repository)
 *
 * Note:
 * This is PRELIMINARY validation (fast feedback to user).
 * FINAL validation happens in Handler (atomic, in transaction).
 * This protects against race conditions.
 */
final class GiftAvailableValidator extends ConstraintValidator
{
    public function __construct(
        private GiftRepositoryInterface $giftRepository
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof GiftAvailable) {
            throw new UnexpectedTypeException($constraint, GiftAvailable::class);
        }

        // Null/empty values are valid (use #[Assert\NotBlank] for required fields)
        if (null === $value || '' === $value) {
            return;
        }

        // Value should be a string (UUID)
        if (!is_string($value)) {
            return;
        }

        // Try to create GiftId Value Object (validates UUID format)
        try {
            $giftId = new GiftId($value);
        } catch (\InvalidArgumentException $e) {
            // Invalid UUID format - let #[Assert\Uuid] handle this
            return;
        }

        // Load Gift entity from repository
        $gift = $this->giftRepository->findById($giftId);

        if (!$gift) {
            $this->context
                ->buildViolation($constraint->notFoundMessage)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
            return;
        }

        // DELEGATE TO DOMAIN - No duplication!
        // Business rule "stock > 0" is defined ONLY in Gift::canBeAttributed()
        if (!$gift->canBeAttributed()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $gift->getName())
                ->addViolation();
        }

        // If we reach here, preliminary validation passed
        // Note: Final validation happens in Handler (atomic, in transaction)
    }
}
