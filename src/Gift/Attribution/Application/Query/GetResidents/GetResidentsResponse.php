<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetResidents;

final readonly class GetResidentsResponse
{
    public int $totalPages;
    public bool $hasNextPage;
    public bool $hasPreviousPage;

    public function __construct(
        public array $residents,
        public int $currentPage,
        public int $perPage,
        public int $total,
    ) {
        $this->totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;
        $this->hasNextPage = $currentPage < $this->totalPages;
        $this->hasPreviousPage = $currentPage > 1;
    }

    public function toArray(): array
    {
        return [
            'residents' => $this->residents,
            'pagination' => [
                'currentPage' => $this->currentPage,
                'perPage' => $this->perPage,
                'total' => $this->total,
                'totalPages' => $this->totalPages,
                'hasNextPage' => $this->hasNextPage,
                'hasPreviousPage' => $this->hasPreviousPage,
            ],
        ];
    }
}
