<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\PlaceOrder;

final readonly class PlaceOrderCommand
{
    public function __construct(
        public string $cartId,
        public string $customerEmail,
        public string $shippingStreet,
        public string $shippingCity,
        public string $shippingPostalCode,
        public string $shippingCountry,
    ) {
    }
}
