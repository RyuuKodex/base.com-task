<?php

declare(strict_types=1);

namespace App\Order\Application\Command;

use App\Order\Application\DTO\OrderDTO;

final readonly class ProcessOrderCommand
{
    public function __construct(
        public OrderDTO $orderDTO
    ) {}
}
