<?php

declare(strict_types=1);

namespace App\Order\Ordering\Application\PlaceOrder;

use App\Order\Cart\Domain\Exception\CartNotFoundException;
use App\Order\Cart\Domain\Port\Out\CartRepositoryInterface;
use App\Order\Catalog\Domain\Exception\InsufficientStockException;
use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use App\Order\Ordering\Domain\Exception\InvalidOrderStateException;
use App\Order\Ordering\Domain\Model\Order;
use App\Order\Ordering\Domain\Model\OrderItem;
use App\Order\Ordering\Domain\Port\Out\OrderRepositoryInterface;
use App\Order\Ordering\Domain\ValueObject\ShippingAddress;
use App\Shared\Domain\Port\Out\IdGeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class PlaceOrderCommandHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private OrderRepositoryInterface $orderRepository,
        private IdGeneratorInterface $idGenerator,
    ) {
    }

    public function __invoke(PlaceOrderCommand $command): void
    {
        $cart = $this->cartRepository->find($command->cartId);
        if ($cart === null) {
            throw CartNotFoundException::withId($command->cartId);
        }

        if ($cart->isEmpty()) {
            throw InvalidOrderStateException::emptyCart($command->cartId);
        }

        // Decrease stock for each item
        foreach ($cart->getItems() as $cartItem) {
            $product = $this->productRepository->find($cartItem->getProductId());
            if ($product === null) {
                throw new \DomainException(sprintf('Product "%s" no longer exists', $cartItem->getProductId()));
            }

            $product->decreaseStock($cartItem->getQuantity());
            $this->productRepository->save($product);
        }

        // Create order items from cart items
        $orderItems = [];
        foreach ($cart->getItems() as $cartItem) {
            $orderItems[] = OrderItem::create(
                $this->idGenerator->generate(),
                $cartItem->getProductId(),
                $cartItem->getProductName(),
                $cartItem->getUnitPrice(),
                $cartItem->getQuantity(),
            );
        }

        $shippingAddress = new ShippingAddress(
            $command->shippingStreet,
            $command->shippingCity,
            $command->shippingPostalCode,
            $command->shippingCountry,
        );

        $order = Order::place(
            $this->idGenerator->generate(),
            $cart->getCustomerId(),
            $command->customerEmail,
            $orderItems,
            $shippingAddress,
        );

        $this->orderRepository->save($order);

        // Clear cart after successful order
        $cart->clear();
        $this->cartRepository->save($cart);
    }
}
