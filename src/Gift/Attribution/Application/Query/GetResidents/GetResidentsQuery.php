<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetResidents;

final readonly class GetResidentsQuery
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public string $searchTerm = '',
    ) {
    }
}
