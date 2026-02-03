<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\GetCart;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Query Handler.
 *
 * Handles the execution of GetCartQuery.
 * Contains the read logic to fetch and return data.
 */
#[AsMessageHandler]
final readonly class GetCartQueryHandler
{
    public function __construct(
        // Inject your dependencies here (repositories, services, etc.)
    ) {
    }

    public function __invoke(GetCartQuery $query): GetCartResponse
    {
        // TODO: Implement your read logic here

        // Example:
        // $data = $this->repository->findById($query->id);
        //
        // return new GetCartResponse(
        //     id: $data->getId(),
        //     name: $data->getName(),
        //     email: $data->getEmail()
        // );

        throw new \RuntimeException('Query handler not yet implemented');
    }
}
