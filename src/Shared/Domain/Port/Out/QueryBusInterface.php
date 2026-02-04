<?php

declare(strict_types=1);

namespace App\Shared\Domain\Port\Out;

use App\Shared\Application\Query\QueryInterface;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
