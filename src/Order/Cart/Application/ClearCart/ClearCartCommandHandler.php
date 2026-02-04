<?php

declare(strict_types=1);

namespace App\Order\Cart\Application\ClearCart;

use App\Order\Cart\Domain\Exception\CartNotFoundException;
use App\Order\Cart\Domain\Port\Out\CartRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @implements CommandHandlerInterface<ClearCartCommand, void>
 */
#[AsMessageHandler(bus: 'command.bus')]
final readonly class ClearCartCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
    ) {
    }

    public function __invoke(ClearCartCommand $command): void
    {
        $cart = $this->cartRepository->find($command->cartId);
        if ($cart === null) {
            throw CartNotFoundException::withId($command->cartId);
        }

        $cart->clear();

        $this->cartRepository->save($cart);
    }
}
