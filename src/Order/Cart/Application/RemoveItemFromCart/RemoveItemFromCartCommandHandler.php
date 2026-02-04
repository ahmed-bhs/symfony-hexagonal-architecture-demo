<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\RemoveItemFromCart;

use App\Order\Cart\Domain\Exception\CartNotFoundException;
use App\Order\Cart\Domain\Port\Out\CartRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandlerInterface<RemoveItemFromCartCommand, void>
 */
#[AsMessageHandler(bus: 'command.bus')]
final readonly class RemoveItemFromCartCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
    ) {
    }

    public function __invoke(RemoveItemFromCartCommand $command): void
    {
        $cart = $this->cartRepository->find($command->cartId);
        if ($cart === null) {
            throw CartNotFoundException::withId($command->cartId);
        }

        $cart->removeItem($command->productId);

        $this->cartRepository->save($cart);
    }
}
