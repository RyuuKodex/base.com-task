<?php

declare(strict_types=1);

namespace App\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;

interface MarketplaceStrategyInterface
{
    public function supports(Marketplace $marketplace): bool;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function processOrderData(array $data): array;
}
