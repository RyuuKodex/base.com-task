<?php

declare(strict_types=1);

namespace App\Order\Domain\Event;

use App\Order\Domain\Model\OrderId;

final readonly class OrderCreatedEvent
{
    public function __construct(
        public OrderId $orderId,
        public int $externalId
    ) {}
}
