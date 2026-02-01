<?php

declare(strict_types=1);

namespace App\Gift\Request\Infrastructure\Adapter\In\Web\Controller;

use App\Gift\Request\Application\Command\SubmitGiftRequest\SubmitGiftRequestCommand;
use App\Gift\Request\Infrastructure\Adapter\In\Web\Form\GiftRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * UI Layer - Web Controller.
 *
 * Part of the UI (User Interface) layer in hexagonal architecture.
 * Responsible for:
 * - Handling HTTP requests
 * - Validating input
 * - Calling use cases/command handlers
 * - Rendering responses (templates, JSON, etc.)
 *
 * This controller is a PRIMARY ADAPTER (driving adapter) that drives the application core.
 *
 * ALTERNATIVE APPROACH (API-FIRST with MapRequestPayload):
 * For a cleaner hexagonal architecture, consider using MapRequestPayload for API endpoints.
 * This approach provides better separation of concerns and reusability.
 *
 * Example with MapRequestPayload (REST API approach):
 *
 * use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
 *
 * #[Route('/api/gift-requests', name: 'api.gift.request.create', methods: ['POST'])]
 * public function __invoke(
 *     #[MapRequestPayload] SubmitGiftRequestCommand $command
 * ): Response {
 *     try {
 *         $this->commandBus->dispatch($command);
 *
 *         return $this->json([
 *             'status' => 'success',
 *             'message' => 'Request submitted successfully'
 *         ], Response::HTTP_CREATED);
 *
 *     } catch (\InvalidArgumentException $e) {
 *         return $this->json([
 *             'status' => 'error',
 *             'message' => $e->getMessage()
 *         ], Response::HTTP_BAD_REQUEST);
 *     }
 * }
 *
 * With this approach:
 * - HTTP JSON request is automatically mapped to Command
 * - Command is immutable (readonly)
 * - Same Command can be used from CLI, Queue, GraphQL, etc.
 * - Controller is ultra-thin (3 lines of logic)
 * - Better testability (no Form mocking needed)
 *
 * Example HTTP Request:
 * POST /api/gift-requests
 * Content-Type: application/json
 * {
 *   "requesterName": "John Doe",
 *   "requesterEmail": "john@example.com",
 *   "requesterPhone": "0612345678",
 *   "requestedGift": "Bicycle",
 *   "motivation": "For commuting to work"
 * }
 */
#[Route('/gift-request', name: 'app.gift.request.form')]
final class GiftRequestFormController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(GiftRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $command = new SubmitGiftRequestCommand(
                requesterName: $data['requesterName'],
                requesterEmail: $data['requesterEmail'],
                requesterPhone: $data['requesterPhone'] ?? '',
                requestedGift: $data['requestedGift'],
                motivation: $data['motivation'],
            );

            try {
                $this->commandBus->dispatch($command);

                $this->addFlash('success', 'Your request has been submitted successfully! We will contact you shortly.');
                return $this->redirectToRoute('app.gift.request.form');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while submitting your request.');
            }
        }

        return $this->render('gift/request/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
