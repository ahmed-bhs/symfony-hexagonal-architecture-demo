<?php

declare(strict_types=1);

namespace App\Tests\Integration\Gift\Request\Application;

use App\Gift\Request\Application\Command\SubmitGiftRequest\SubmitGiftRequestCommand;
use App\Gift\Request\Application\Command\SubmitGiftRequest\SubmitGiftRequestCommandHandler;
use App\Gift\Request\Domain\Model\GiftRequest;
use App\Gift\Request\Domain\Port\Out\GiftRequestRepositoryInterface;
use App\Tests\Fake\Generator\FakeIdGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for SubmitGiftRequestCommandHandler.
 *
 * Tests that the handler correctly orchestrates the submission of a gift request.
 * Note: Validation is done by ValidationMiddleware (tested separately).
 */
final class SubmitGiftRequestHandlerTest extends TestCase
{
    private InMemoryGiftRequestRepository $repository;
    private FakeIdGenerator $idGenerator;
    private SubmitGiftRequestCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryGiftRequestRepository();
        $this->idGenerator = new FakeIdGenerator();

        $this->handler = new SubmitGiftRequestCommandHandler(
            $this->idGenerator,
            $this->repository,
        );
    }

    #[Test]
    public function it_submits_valid_gift_request(): void
    {
        // Arrange
        $command = new SubmitGiftRequestCommand(
            requesterName: 'John Doe',
            requesterEmail: 'john@example.com',
            requesterPhone: '0612345678',
            requestedGift: 'Vélo électrique',
            motivation: 'I would like to receive a bicycle to commute in an eco-friendly way.'
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        $requests = $this->repository->findAll();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('John Doe', $request->getRequesterName());
        $this->assertEquals('john@example.com', $request->getRequesterEmail()->value);
        $this->assertEquals('0612345678', $request->getRequesterPhone());
        $this->assertEquals('Vélo électrique', $request->getRequestedGift());
    }

}

/**
 * In-Memory Repository for GiftRequest (test double).
 */
final class InMemoryGiftRequestRepository implements GiftRequestRepositoryInterface
{
    private array $requests = [];

    public function save(GiftRequest $giftRequest): void
    {
        $this->requests[$giftRequest->getId()] = $giftRequest;
    }

    public function find(string $id): ?GiftRequest
    {
        return $this->requests[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->requests);
    }
}
