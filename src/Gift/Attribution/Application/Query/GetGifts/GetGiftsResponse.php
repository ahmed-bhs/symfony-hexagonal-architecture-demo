<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetGifts;

final readonly class GetGiftsResponse
{
    public function __construct(
        public array $gifts,
    ) {
    }
}
