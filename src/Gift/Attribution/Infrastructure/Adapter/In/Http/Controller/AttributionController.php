<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\In\Http\Controller;

use App\Gift\Attribution\Infrastructure\Adapter\In\Http\Request\AttributeGiftRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Controller: Manual Gift Attribution (CQRS Command).
 *
 * HEXAGONAL ARCHITECTURE - UI LAYER:
 * This controller demonstrates the pure hexagonal architecture approach:
 *
 * Flow:
 * 1. HTTP Request -> Symfony validates AttributeGiftRequest (UI DTO)
 * 2. Request DTO -> Converts to AttributeGiftCommand (Application Command)
 * 3. Command Bus -> Dispatches to Handler (Application)
 * 4. Handler -> Executes business logic (Domain)
 * 5. Response -> Returns JSON (UI)
 *
 * Validation levels:
 * - Level 1 (UI): Request DTO validation (#[MapRequestPayload])
 *   - Format validation (#[Assert\NotBlank], #[Assert\Uuid])
 *   - Custom validation (#[GiftAvailable] - preliminary)
 * - Level 2 (Handler): Final atomic validation (inside transaction)
 *   - Gift::decreaseStock() validates and decreases stock
 *   - Protects against race conditions
 *
 * Benefits:
 * - Controller is thin (no business logic)
 * - Application Command stays pure (no Symfony)
 * - Two-level validation (fast feedback + race condition protection)
 * - Clean separation of concerns
 */
#[Route('/api/attributions', name: 'api_attributions_')]
final class AttributionController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {}

    /**
     * Manually attribute a gift to a resident.
     *
     * POST /api/attributions
     * Body: {"residentId": "uuid", "giftId": "uuid"}
     *
     * Validation flow:
     * 1. Symfony validates AttributeGiftRequest
     * 2. GiftAvailableValidator checks stock (preliminary)
     * 3. If valid -> toCommand() creates pure Command
     * 4. Handler validates stock again (atomic, in transaction)
     *
     * Responses:
     * - 201: Gift attributed successfully
     * - 400: Validation failed (invalid format, stock unavailable, etc.)
     * - 404: Resident or Gift not found
     * - 500: Internal server error
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]  // Symfony validates automatically
        AttributeGiftRequest $request
    ): JsonResponse {
        try {
            // If we reach here, validation passed (format + preliminary stock check)

            // Convert Request DTO (UI) to Command (Application)
            $command = $request->toCommand();

            // Dispatch to Handler (Application layer)
            // Handler will do final atomic validation + stock decrease
            $this->commandBus->dispatch($command);

            return new JsonResponse([
                'success' => true,
                'message' => 'Gift attributed successfully',
            ], Response::HTTP_CREATED);

        } catch (ValidationFailedException $e) {
            // Symfony Validator failed (format, preliminary stock check, etc.)
            $violations = [];
            foreach ($e->getViolations() as $violation) {
                $violations[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            return new JsonResponse([
                'error' => 'Validation failed',
                'violations' => $violations,
            ], Response::HTTP_BAD_REQUEST);

        } catch (\InvalidArgumentException $e) {
            // Resident or Gift not found
            return new JsonResponse([
                'error' => 'Not found',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);

        } catch (\DomainException $e) {
            // Final validation failed (stock exhausted between preliminary check and handler)
            // This handles race conditions
            return new JsonResponse([
                'error' => 'Attribution failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Throwable $e) {
            // Unexpected error
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
