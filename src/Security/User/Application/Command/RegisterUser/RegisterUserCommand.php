<?php

declare(strict_types=1);

namespace App\Security\User\Application\Command\RegisterUser;

use App\Shared\Application\Command\CommandInterface;

final readonly class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $plainPassword,
        public array $roles = ['ROLE_USER'],
    ) {}
}
