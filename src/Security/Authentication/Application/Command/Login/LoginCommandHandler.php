<?php

declare(strict_types=1);

namespace App\Security\Authentication\Application\Command\Login;

use App\Security\Authentication\Application\DTO\TokenDTO;
use App\Security\Authentication\Application\Exception\InvalidCredentialsException;
use App\Security\Authentication\Domain\Port\Out\TokenGeneratorInterface;
use App\Security\User\Domain\Port\Out\PasswordHasherInterface;
use App\Security\User\Domain\Port\Out\UserRepositoryInterface;
use App\Security\User\Domain\ValueObject\Email;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Command Handler: Login
 *
 * Authenticates user and generates JWT token.
 *
 * @implements CommandHandlerInterface<LoginCommand, TokenDTO>
 */
#[AsMessageHandler]
final readonly class LoginCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private TokenGeneratorInterface $tokenGenerator,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function __invoke(LoginCommand $command): TokenDTO
    {
        // Find user by email
        $email = new Email($command->email);
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        // Verify password
        if (!$user->verifyPassword($command->plainPassword, $this->passwordHasher)) {
            throw new InvalidCredentialsException();
        }

        // Record login
        $user->recordLogin();
        $this->userRepository->save($user);

        // Generate JWT token
        $token = $this->tokenGenerator->generateToken($user);

        return new TokenDTO(
            token: $token,
            userId: $user->id()->value(),
            email: $user->email()->value(),
            roles: $user->roles(),
        );
    }
}
