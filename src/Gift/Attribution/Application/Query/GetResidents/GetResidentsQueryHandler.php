<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Application\Query\GetResidents;

use App\Gift\Attribution\Domain\Port\In\GetResidentsUseCaseInterface;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use App\Shared\Pagination\Domain\ValueObject\Page;
use App\Shared\Pagination\Domain\ValueObject\PerPage;
use App\Shared\Search\Domain\ValueObject\SearchTerm;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetResidentsQueryHandler implements GetResidentsUseCaseInterface
{
    public function __construct(
        private ResidentRepositoryInterface $residentRepository,
    ) {
    }

    public function __invoke(GetResidentsQuery $query): GetResidentsResponse
    {
        $page = new Page($query->page);
        $perPage = new PerPage($query->perPage);
        $searchTerm = new SearchTerm($query->searchTerm);

        $paginatedResult = $this->residentRepository->searchPaginated($searchTerm, $page, $perPage);

        return new GetResidentsResponse(
            residents: $paginatedResult->items,
            currentPage: $paginatedResult->page->value,
            perPage: $paginatedResult->perPage->value,
            total: $paginatedResult->total->value,
        );
    }
}
