<?php

declare(strict_types=1);

namespace App\Order\Application\Query;

final readonly class GetOrdersQuery
{
    public function __construct(
        public ?string $marketplace = null,
        public int $limit = 10,
        public int $offset = 0
    ) {}
}
