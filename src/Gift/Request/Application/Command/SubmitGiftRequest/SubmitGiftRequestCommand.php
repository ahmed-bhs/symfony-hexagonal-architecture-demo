<?php

declare(strict_types=1);

namespace App\Gift\Request\Application\Command\SubmitGiftRequest;

use App\Shared\Application\Command\CommandInterface;

final readonly class SubmitGiftRequestCommand implements CommandInterface
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
