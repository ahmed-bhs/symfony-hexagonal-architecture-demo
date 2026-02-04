<?php

declare(strict_types=1);

namespace App\Security\Authentication\Application\Command\Login;

use App\Shared\Application\Command\CommandInterface;

final readonly class LoginCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $plainPassword,
    ) {}
}
