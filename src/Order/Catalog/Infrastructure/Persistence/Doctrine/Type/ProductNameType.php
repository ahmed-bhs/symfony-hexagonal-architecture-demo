<?php

declare(strict_types=1);

namespace App\Order\Catalog\Infrastructure\Persistence\Doctrine\Type;

use App\Order\Catalog\Domain\ValueObject\ProductName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProductNameType extends Type
{
    public const NAME = 'product_name';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 200]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductName
    {
        if ($value === null) {
            return null;
        }

        return new ProductName((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductName) {
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
