<?php

declare(strict_types=1);

namespace App\Order\Domain\Repository;

use App\Order\Domain\Model\Order;

interface BaselinkerOrderRepositoryInterface
{
    /**
     * @return Order[]
     */
    public function getOrders(): array;
}
