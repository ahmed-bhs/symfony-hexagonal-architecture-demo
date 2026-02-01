<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Domain\Model;

use App\Gift\Attribution\Domain\ValueObject\Age;
use App\Shared\Domain\ValueObject\Email;
use App\Gift\Attribution\Domain\ValueObject\ResidentId;

/**
 * Domain Entity.
 *
 * Represents a domain concept with identity and lifecycle.
 * Contains business logic and enforces invariants.
 *
 * In hexagonal architecture, entities are part of the Domain layer (core)
 * and are completely independent of infrastructure concerns.
 *
 * IMPORTANT: This entity is PURE - no framework dependencies.
 * Doctrine ORM mapping is configured separately in:
 * Infrastructure/Persistence/Doctrine/Orm/Mapping/Resident.orm.xml
 */
class Resident
{
    private ResidentId $id;
    private string $firstName;
    private string $lastName;
    private Age $age;
    private Email $email;

    public function __construct(
        ResidentId $id,
        string $firstName,
        string $lastName,
        Age $age,
        Email $email
    ) {
        if (empty(trim($firstName))) {
            throw new \InvalidArgumentException('First name cannot be empty');
        }

        if (empty(trim($lastName))) {
            throw new \InvalidArgumentException('Last name cannot be empty');
        }

        $this->id = $id;
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
        $this->age = $age;
        $this->email = $email;
    }

    public static function create(
        ResidentId $id,
        string $firstName,
        string $lastName,
        Age $age,
        Email $email
    ): self {
        return new self(
            $id,
            $firstName,
            $lastName,
            $age,
            $email
        );
    }

    public function getId(): ResidentId
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getAge(): Age
    {
        return $this->age;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function isAdult(): bool
    {
        return $this->age->isAdult();
    }

    public function isSenior(): bool
    {
        return $this->age->isSenior();
    }

    public function isChild(): bool
    {
        return $this->age->isChild();
    }
}
