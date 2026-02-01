<?php

declare(strict_types=1);

namespace App\Gift\Request\Domain\Model;

use App\Gift\Request\Domain\Event\GiftRequestSubmitted;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\Email;

/**
 * Aggregate Root: Gift Request.
 *
 * An Aggregate Root is the entry point to an aggregate - a cluster of domain
 * objects treated as a single unit for data changes.
 *
 * Responsibilities:
 * - Enforce business invariants
 * - Record domain events when important business operations occur
 * - Coordinate changes to entities within the aggregate boundary
 *
 * Domain Events:
 * - Uses AggregateRoot trait to collect events
 * - Events are recorded during business operations (create, approve, reject)
 * - Infrastructure pulls and publishes events after successful persistence
 *
 * In hexagonal architecture:
 * - PURE Domain layer (no framework dependencies)
 * - Doctrine ORM mapping configured separately in XML
 * - Events published automatically by infrastructure
 *
 * Note: Not final to allow Doctrine lazy loading with ghost objects (ORM 3.x)
 */
class GiftRequest
{
    use AggregateRoot;
    private string $id;
    private string $requesterName;
    private Email $requesterEmail;
    private string $requesterPhone;
    private string $requestedGift;
    private string $motivation;
    private string $status;
    private \DateTimeImmutable $createdAt;

    private function __construct(
        string $id,
        string $requesterName,
        Email $requesterEmail,
        string $requesterPhone,
        string $requestedGift,
        string $motivation,
    ) {
        $this->id = $id;
        $this->requesterName = $requesterName;
        $this->requesterEmail = $requesterEmail;
        $this->requesterPhone = $requesterPhone;
        $this->requestedGift = $requestedGift;
        $this->motivation = $motivation;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        string $id,
        string $requesterName,
        string $requesterEmail,
        string $requesterPhone,
        string $requestedGift,
        string $motivation,
    ): self {
        if (empty($requesterName)) {
            throw new \InvalidArgumentException('Requester name cannot be empty');
        }

        // Email validation delegated to Value Object
        $email = new Email($requesterEmail);

        if (empty($requestedGift)) {
            throw new \InvalidArgumentException('Requested gift cannot be empty');
        }
        if (empty($motivation)) {
            throw new \InvalidArgumentException('Motivation cannot be empty');
        }

        $request = new self($id, $requesterName, $email, $requesterPhone, $requestedGift, $motivation);

        // Record domain event - will be published by infrastructure after successful flush
        $request->recordThat(new GiftRequestSubmitted(
            giftRequestId: $request->id,
            requesterName: $request->requesterName,
            requesterEmail: $request->requesterEmail->value,
            requestedGift: $request->requestedGift,
            submittedAt: $request->createdAt
        ));

        return $request;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRequesterName(): string
    {
        return $this->requesterName;
    }

    public function getRequesterEmail(): Email
    {
        return $this->requesterEmail;
    }

    public function getRequesterPhone(): string
    {
        return $this->requesterPhone;
    }

    public function getRequestedGift(): string
    {
        return $this->requestedGift;
    }

    public function getMotivation(): string
    {
        return $this->motivation;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function approve(): void
    {
        if ($this->status === 'approved') {
            throw new \DomainException('The request is already approved');
        }
        $this->status = 'approved';
    }

    public function reject(): void
    {
        if ($this->status === 'rejected') {
            throw new \DomainException('The request is already rejected');
        }
        $this->status = 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
