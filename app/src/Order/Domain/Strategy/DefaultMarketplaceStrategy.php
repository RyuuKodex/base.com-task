<?php

declare(strict_types=1);

namespace App\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;

final class DefaultMarketplaceStrategy implements MarketplaceStrategyInterface
{
    public function supports(Marketplace $marketplace): bool
    {
        return true;
    }

    public function processOrderData(array $data): array
    {
        return $data;
    }
}
