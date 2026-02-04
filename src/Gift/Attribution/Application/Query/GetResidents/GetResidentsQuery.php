<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetResidents;

use App\Shared\Application\Query\QueryInterface;

final readonly class GetResidentsQuery implements QueryInterface
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public string $searchTerm = '',
    ) {
    }
}
