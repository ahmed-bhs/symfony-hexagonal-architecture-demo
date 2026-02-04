<?php

declare(strict_types=1);

namespace App\Security\Authentication\Application\Query\GetCurrentUser;

use App\Shared\Application\Query\QueryInterface;

final readonly class GetCurrentUserQuery implements QueryInterface
{
    public function __construct(
        public string $userId
    ) {}
}
