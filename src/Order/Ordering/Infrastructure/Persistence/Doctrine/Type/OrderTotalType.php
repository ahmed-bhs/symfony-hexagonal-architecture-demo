<?php

declare(strict_types=1);

namespace App\Order\Ordering\Infrastructure\Persistence\Doctrine\Type;

use App\Order\Ordering\Domain\ValueObject\OrderTotal;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class OrderTotalType extends Type
{
    public const NAME = 'order_total';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?OrderTotal
    {
        if ($value === null) {
            return null;
        }

        return new OrderTotal((int) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof OrderTotal) {
            return $value->amount;
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
