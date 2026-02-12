<?php

declare(strict_types=1);

namespace App\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;

final class MarketplaceStrategyRegistry
{
    /** @var MarketplaceStrategyInterface[] */
    private array $strategies;

    /**
     * @param iterable<MarketplaceStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies)
    {
        $this->strategies = iterator_to_array($strategies);
    }

    public function getStrategy(Marketplace $marketplace): MarketplaceStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($marketplace) && !($strategy instanceof DefaultMarketplaceStrategy)) {
                return $strategy;
            }
        }

        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof DefaultMarketplaceStrategy) {
                return $strategy;
            }
        }

        throw new \RuntimeException(sprintf('No strategy found for marketplace: %s', $marketplace->value));
    }
}
