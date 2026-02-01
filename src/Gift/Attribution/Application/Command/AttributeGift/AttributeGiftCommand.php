<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Command\AttributeGift;

use App\Gift\Attribution\Domain\ValueObject\GiftId;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;

final readonly class AttributeGiftCommand
{
    public function __construct(
        public ResidentId $residentId,
        public GiftId $giftId,
    ) {
    }
}
