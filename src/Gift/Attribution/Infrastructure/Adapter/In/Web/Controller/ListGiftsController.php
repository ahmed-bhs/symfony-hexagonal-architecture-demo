<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\In\Web\Controller;

use App\Gift\Attribution\Application\Query\GetGifts\GetGiftsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller to display the list of gifts.
 *
 * HEXAGONAL ARCHITECTURE:
 * This controller follows hexagonal principles:
 * - Depends on Application layer (Query)
 * - Uses MessageBus for CQRS pattern
 * - No direct access to repositories
 * - No knowledge of infrastructure details
 *
 * ALTERNATIVE APPROACH (API-FIRST with MapRequestPayload):
 * For REST API endpoints with empty queries, you can still use the same pattern.
 *
 * Example with API approach:
 *
 * #[Route('/api/gifts', name: 'api.gift.attribution.list_gifts', methods: ['GET'])]
 * public function __invoke(): Response {
 *     try {
 *         $query = new GetGiftsQuery();
 *         $envelope = $this->queryBus->dispatch($query);
 *         $handledStamp = $envelope->last(HandledStamp::class);
 *         $response = $handledStamp->getResult();
 *
 *         return $this->json([
 *             'gifts' => array_map(
 *                 fn($gift) => [
 *                     'id' => $gift->getId(),
 *                     'name' => $gift->getName(),
 *                     'description' => $gift->getDescription(),
 *                     'quantity' => $gift->getQuantity(),
 *                     'inStock' => $gift->isInStock(),
 *                 ],
 *                 $response->gifts
 *             )
 *         ]);
 *
 *     } catch (\Exception $e) {
 *         return $this->json([
 *             'status' => 'error',
 *             'message' => 'Error retrieving gifts'
 *         ], Response::HTTP_INTERNAL_SERVER_ERROR);
 *     }
 * }
 *
 * Example HTTP Request:
 * GET /api/gifts
 *
 * Response:
 * {
 *   "gifts": [
 *     {
 *       "id": "123e4567-e89b-12d3-a456-426614174000",
 *       "name": "Bicycle",
 *       "description": "City bicycle",
 *       "quantity": 5,
 *       "inStock": true
 *     }
 *   ]
 * }
 */
#[Route('/gifts', name: 'app.gift.attribution.list_gifts')]
final class ListGiftsController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
    ) {
    }

    public function __invoke(): Response
    {
        // Execute query to retrieve all gifts
        $query = new GetGiftsQuery();
        $envelope = $this->queryBus->dispatch($query);

        // Get response from handler
        $handledStamp = $envelope->last(HandledStamp::class);
        $response = $handledStamp->getResult();

        // Render template
        return $this->render('gift/attribution/list_gifts.html.twig', [
            'gifts' => $response->gifts,
        ]);
    }
}
