<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Strategy\AllegroStrategy;
use App\Order\Domain\Strategy\AmazonStrategy;
use App\Order\Domain\Strategy\DefaultMarketplaceStrategy;
use App\Order\Domain\Strategy\EbayStrategy;
use App\Order\Domain\Strategy\MarketplaceStrategyRegistry;
use PHPUnit\Framework\TestCase;

final class MarketplaceStrategyRegistryTest extends TestCase
{
    public function testGetStrategyReturnsSpecificStrategy(): void
    {
        $allegroStrategy = new AllegroStrategy();
        $ebayStrategy = new EbayStrategy();
        $amazonStrategy = new AmazonStrategy();
        $defaultStrategy = new DefaultMarketplaceStrategy();

        $registry = new MarketplaceStrategyRegistry([$allegroStrategy, $ebayStrategy, $amazonStrategy, $defaultStrategy]);

        $this->assertInstanceOf(AllegroStrategy::class, $registry->getStrategy(Marketplace::ALLEGRO));
        $this->assertInstanceOf(EbayStrategy::class, $registry->getStrategy(Marketplace::EBAY));
        $this->assertInstanceOf(AmazonStrategy::class, $registry->getStrategy(Marketplace::AMAZON));
    }

    public function testGetStrategyReturnsDefaultStrategy(): void
    {
        $allegroStrategy = new AllegroStrategy();
        $defaultStrategy = new DefaultMarketplaceStrategy();

        $registry = new MarketplaceStrategyRegistry([$allegroStrategy, $defaultStrategy]);

        $strategy = $registry->getStrategy(Marketplace::OTHER);
        $this->assertInstanceOf(DefaultMarketplaceStrategy::class, $strategy);
    }
}
