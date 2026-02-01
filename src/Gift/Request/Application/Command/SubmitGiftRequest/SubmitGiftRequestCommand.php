<?php

declare(strict_types=1);

namespace App\Gift\Request\Application\Command\SubmitGiftRequest;

final readonly class SubmitGiftRequestCommand
{
    public function __construct(
        public string $requesterName,
        public string $requesterEmail,
        public string $requesterPhone,
        public string $requestedGift,
        public string $motivation,
    ) {
    }
}
