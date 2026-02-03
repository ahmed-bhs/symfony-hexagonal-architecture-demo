<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\AddItemToCart;

use App\Order\Cart\Domain\Exception\CartNotFoundException;
use App\Order\Cart\Domain\Port\Out\CartRepositoryInterface;
use App\Order\Catalog\Domain\Exception\InsufficientStockException;
use App\Order\Catalog\Domain\Exception\ProductNotFoundException;
use App\Order\Catalog\Domain\Port\Out\ProductRepositoryInterface;
use App\Shared\Domain\Port\Out\IdGeneratorInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class AddItemToCartCommandHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private IdGeneratorInterface $idGenerator,
    ) {
    }

    public function __invoke(AddItemToCartCommand $command): void
    {
        $cart = $this->cartRepository->find($command->cartId);
        if ($cart === null) {
            throw CartNotFoundException::withId($command->cartId);
        }

        $product = $this->productRepository->find($command->productId);
        if ($product === null) {
            throw ProductNotFoundException::withId($command->productId);
        }

        if (!$product->isAvailable($command->quantity)) {
            throw InsufficientStockException::forProduct(
                $product->getId(),
                (string) $product->getName(),
                $command->quantity,
                $product->getStock(),
            );
        }

        $cart->addItem(
            $this->idGenerator->generate(),
            $product->getId(),
            (string) $product->getName(),
            $product->getPrice()->amount,
            $command->quantity,
        );

        $this->cartRepository->save($cart);
    }
}
