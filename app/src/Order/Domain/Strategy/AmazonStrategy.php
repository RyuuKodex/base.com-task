<?php

declare(strict_types=1);

namespace App\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;

final class AmazonStrategy implements MarketplaceStrategyInterface
{
    public function supports(Marketplace $marketplace): bool
    {
        return Marketplace::AMAZON === $marketplace;
    }

    public function processOrderData(array $data): array
    {
        $data['processed_by_strategy'] = 'amazon';

        return $data;
    }
}
