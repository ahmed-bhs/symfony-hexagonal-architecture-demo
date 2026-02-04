<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Command\AttributeGift;

use App\Gift\Attribution\Domain\ValueObject\GiftId;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;
use App\Shared\Application\Command\CommandInterface;

final readonly class AttributeGiftCommand implements CommandInterface
{
    public function __construct(
        public ResidentId $residentId,
        public GiftId $giftId,
    ) {
    }
}
