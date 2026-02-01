<?php

declare(strict_types=1);

namespace App\Tests\Integration\Gift\Attribution\Application;

use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommand;
use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommandHandler;
use App\Gift\Attribution\Domain\Model\GiftAttribution;
use App\Gift\Attribution\Domain\Model\Gift;
use App\Gift\Attribution\Domain\Model\Resident;
use App\Gift\Attribution\Domain\Port\Out\GiftAttributionRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface;
use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use App\Gift\Attribution\Domain\ValueObject\Age;
use App\Gift\Attribution\Domain\ValueObject\GiftId;
use App\Shared\Domain\ValueObject\Email;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;
use App\Tests\Fake\Generator\FakeIdGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for AttributeGiftCommandHandler.
 *
 * MINIMUM REQUIRED:
 * - Tests that the handler correctly orchestrates Domain + Repositories
 * - Uses in-memory repositories (InMemory)
 * - Uses FakeIdGenerator for predictable IDs
 */
final class AttributeGiftHandlerTest extends TestCase
{
    private InMemoryResidentRepository $residentRepository;
    private InMemoryGiftRepository $giftRepository;
    private InMemoryGiftAttributionRepository $giftAttributionRepository;
    private FakeIdGenerator $idGenerator;
    private AttributeGiftCommandHandler $handler;

    protected function setUp(): void
    {
        $this->residentRepository = new InMemoryResidentRepository();
        $this->giftRepository = new InMemoryGiftRepository();
        $this->giftAttributionRepository = new InMemoryGiftAttributionRepository();
        $this->idGenerator = new FakeIdGenerator();

        $this->handler = new AttributeGiftCommandHandler(
            $this->idGenerator,
            $this->residentRepository,
            $this->giftRepository,
            $this->giftAttributionRepository,
        );
    }

    #[Test]
    public function it_attributes_gift_to_resident(): void
    {
        // Arrange - Valid UUIDs
        $residentId = '550e8400-e29b-41d4-a716-446655440001';
        $giftId = '550e8400-e29b-41d4-a716-446655440002';

        $resident = Resident::create(
            new ResidentId($residentId),
            'John',
            'Doe',
            new Age(30),
            new Email('john@example.com')
        );
        $this->residentRepository->save($resident);

        $gift = Gift::create($giftId, 'Vélo', 'Vélo de ville', 10);
        $this->giftRepository->save($gift);

        $command = new AttributeGiftCommand(
            residentId: new ResidentId($residentId),
            giftId: new GiftId($giftId)
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        $attributions = $this->giftAttributionRepository->findAll();
        $this->assertCount(1, $attributions);

        $attribution = $attributions[0];
        $this->assertEquals('fake-id-1', $attribution->getId());
        $this->assertEquals($residentId, $attribution->getResidentId());
        $this->assertEquals($giftId, $attribution->getGiftId());
    }

    #[Test]
    public function it_rejects_attribution_when_resident_not_found(): void
    {
        // Arrange
        $giftId = '550e8400-e29b-41d4-a716-446655440003';
        $gift = Gift::create($giftId, 'Vélo', 'Vélo de ville', 10);
        $this->giftRepository->save($gift);

        $nonExistentResidentId = '550e8400-e29b-41d4-a716-446655440099';
        $command = new AttributeGiftCommand(
            residentId: new ResidentId($nonExistentResidentId),
            giftId: new GiftId($giftId)
        );

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resident with ID "' . $nonExistentResidentId . '" not found');

        // Act
        $this->handler->__invoke($command);
    }

    #[Test]
    public function it_rejects_attribution_when_gift_not_found(): void
    {
        // Arrange
        $residentId = '550e8400-e29b-41d4-a716-446655440001';

        $resident = Resident::create(
            new ResidentId($residentId),
            'John',
            'Doe',
            new Age(30),
            new Email('john@example.com')
        );
        $this->residentRepository->save($resident);

        $nonExistentGiftId = '550e8400-e29b-41d4-a716-446655440098';
        $command = new AttributeGiftCommand(
            residentId: new ResidentId($residentId),
            giftId: new GiftId($nonExistentGiftId)
        );

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Gift with ID "' . $nonExistentGiftId . '" not found');

        // Act
        $this->handler->__invoke($command);
    }
}

/**
 * In-Memory Repository for Resident (test double).
 */
final class InMemoryResidentRepository implements ResidentRepositoryInterface
{
    private array $residents = [];

    public function save(Resident $resident): void
    {
        $this->residents[$resident->getId()->value] = $resident;
    }

    public function findById(string|ResidentId $id): ?Resident
    {
        $key = $id instanceof ResidentId ? $id->value : $id;
        return $this->residents[$key] ?? null;
    }

    public function delete(Resident $resident): void
    {
        unset($this->residents[$resident->getId()->value]);
    }

    public function findAll(): array
    {
        return array_values($this->residents);
    }

    public function findByEmail(string $email): ?Resident
    {
        foreach ($this->residents as $resident) {
            if ($resident->getEmail()->value === $email) {
                return $resident;
            }
        }
        return null;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findPaginated(\App\Shared\Pagination\Domain\ValueObject\Page $page, \App\Shared\Pagination\Domain\ValueObject\PerPage $perPage): \App\Shared\Pagination\Domain\ValueObject\PaginatedResult
    {
        throw new \RuntimeException('Not implemented for test');
    }

    public function searchPaginated(\App\Shared\Search\Domain\ValueObject\SearchTerm $searchTerm, \App\Shared\Pagination\Domain\ValueObject\Page $page, \App\Shared\Pagination\Domain\ValueObject\PerPage $perPage): \App\Shared\Pagination\Domain\ValueObject\PaginatedResult
    {
        throw new \RuntimeException('Not implemented for test');
    }
}

/**
 * In-Memory Repository for Gift (test double).
 */
final class InMemoryGiftRepository implements GiftRepositoryInterface
{
    private array $gifts = [];

    public function save(Gift $gift): void
    {
        $this->gifts[$gift->getId()] = $gift;
    }

    public function findById(string|GiftId $id): ?Gift
    {
        $key = $id instanceof GiftId ? $id->value() : $id;
        return $this->gifts[$key] ?? null;
    }

    public function delete(Gift $gift): void
    {
        unset($this->gifts[$gift->getId()]);
    }

    public function findAll(): array
    {
        return array_values($this->gifts);
    }

    public function findByName(string $name): ?Gift { return null; }
    public function findAllInStock(): array { return []; }
}

/**
 * In-Memory Repository for GiftAttribution (test double).
 */
final class InMemoryGiftAttributionRepository implements GiftAttributionRepositoryInterface
{
    private array $attributions = [];

    public function save(GiftAttribution $attribution): void
    {
        $this->attributions[$attribution->getId()] = $attribution;
    }

    public function findById(string $id): ?GiftAttribution
    {
        return $this->attributions[$id] ?? null;
    }

    public function delete(GiftAttribution $attribution): void
    {
        unset($this->attributions[$attribution->getId()]);
    }

    public function findAll(): array
    {
        return array_values($this->attributions);
    }
}
