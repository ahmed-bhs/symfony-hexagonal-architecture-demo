<?php

declare(strict_types=1);

namespace App\Order\Ordering\Infrastructure\Persistence\Doctrine\Type;

use App\Order\Ordering\Domain\ValueObject\ShippingAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ShippingAddressType extends Type
{
    public const NAME = 'shipping_address';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ShippingAddress
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode((string) $value, true);

        return new ShippingAddress(
            $data['street'] ?? '',
            $data['city'] ?? '',
            $data['postalCode'] ?? '',
            $data['country'] ?? '',
        );
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ShippingAddress) {
            return json_encode([
                'street' => $value->street,
                'city' => $value->city,
                'postalCode' => $value->postalCode,
                'country' => $value->country,
            ], JSON_THROW_ON_ERROR);
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
