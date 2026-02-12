<?php

declare(strict_types=1);

namespace App\Order\Domain\Repository;

use App\Order\Domain\Model\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function existsByExternalId(int $externalId): bool;

    public function findByExternalId(int $externalId): ?Order;

    /**
     * @return Order[]
     */
    public function findByFilters(?string $marketplace, int $limit, int $offset): array;
}
