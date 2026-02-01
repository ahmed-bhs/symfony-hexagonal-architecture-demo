<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetStatistics;

use App\Gift\Attribution\Domain\Port\In\GetStatisticsUseCaseInterface;
use App\Gift\Attribution\Domain\Port\Out\GiftAttributionRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetStatisticsQueryHandler implements GetStatisticsUseCaseInterface
{
    public function __construct(
        private ResidentRepositoryInterface $residentRepository,
        private GiftRepositoryInterface $giftRepository,
        private GiftAttributionRepositoryInterface $attributionRepository,
    ) {
    }

    public function __invoke(GetStatisticsQuery $query): GetStatisticsResponse
    {
        $residents = $this->residentRepository->findAll();
        $gifts = $this->giftRepository->findAll();
        $attributions = $this->attributionRepository->findAll();

        $children = 0;
        $adults = 0;
        $seniors = 0;

        foreach ($residents as $resident) {
            if ($resident->isChild()) {
                $children++;
            } elseif ($resident->isSenior()) {
                $seniors++;
            } else {
                $adults++;
            }
        }

        return new GetStatisticsResponse(
            totalResidents: count($residents),
            totalGifts: count($gifts),
            totalAttributions: count($attributions),
            childResidents: $children,
            adultResidents: $adults,
            seniorResidents: $seniors,
        );
    }
}
