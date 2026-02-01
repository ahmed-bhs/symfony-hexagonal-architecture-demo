<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetGifts;

use App\Gift\Attribution\Domain\Port\In\GetGiftsUseCaseInterface;
use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetGiftsQueryHandler implements GetGiftsUseCaseInterface
{
    public function __construct(
        private GiftRepositoryInterface $giftRepository,
    ) {
    }

    public function __invoke(GetGiftsQuery $query): GetGiftsResponse
    {
        $gifts = $this->giftRepository->findAll();

        return new GetGiftsResponse($gifts);
    }
}
