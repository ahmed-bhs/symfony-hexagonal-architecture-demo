<?php

declare(strict_types=1);

namespace App\Security\User\Infrastructure\Adapter\Out\Security;

use App\Security\User\Domain\Port\Out\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Symfony Password Hasher (Adapter)
 *
 * Implements PasswordHasherInterface port using Symfony's password hasher.
 * Uses algorithm configured in security.yaml (bcrypt, argon2, etc.)
 */
final readonly class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $hasherFactory,
    ) {}

    public function hash(string $plainPassword): string
    {
        $hasher = $this->hasherFactory->getPasswordHasher('common');

        return $hasher->hash($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        $hasher = $this->hasherFactory->getPasswordHasher('common');

        return $hasher->verify($hashedPassword, $plainPassword);
    }
}
