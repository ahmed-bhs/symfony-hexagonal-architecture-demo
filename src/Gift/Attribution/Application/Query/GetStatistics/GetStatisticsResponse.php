<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetStatistics;

final readonly class GetStatisticsResponse
{
    public function __construct(
        public int $totalResidents,
        public int $totalGifts,
        public int $totalAttributions,
        public int $childResidents,
        public int $adultResidents,
        public int $seniorResidents,
    ) {
    }
}
