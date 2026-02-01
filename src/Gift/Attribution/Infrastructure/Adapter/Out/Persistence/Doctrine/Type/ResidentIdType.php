<?php

declare(strict_types=1);

namespace App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine\Type;

use App\Gift\Attribution\Domain\ValueObject\ResidentId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ResidentIdType extends Type
{
    public const NAME = 'resident_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 36]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ResidentId
    {
        if ($value === null) {
            return null;
        }

        return new ResidentId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ResidentId) {
            return $value->value;
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
