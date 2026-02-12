<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Strategy;

use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Strategy\AllegroStrategy;
use App\Order\Domain\Strategy\AmazonStrategy;
use App\Order\Domain\Strategy\EbayStrategy;
use PHPUnit\Framework\TestCase;

final class MarketplaceStrategiesTest extends TestCase
{
    public function testAllegroStrategy(): void
    {
        $strategy = new AllegroStrategy();
        $this->assertTrue($strategy->supports(Marketplace::ALLEGRO));
        $this->assertFalse($strategy->supports(Marketplace::AMAZON));

        $data = ['order_id' => 1];
        $processed = $strategy->processOrderData($data);
        $this->assertEquals('allegro', $processed['processed_by_strategy']);
    }

    public function testAmazonStrategy(): void
    {
        $strategy = new AmazonStrategy();
        $this->assertTrue($strategy->supports(Marketplace::AMAZON));
        $this->assertFalse($strategy->supports(Marketplace::ALLEGRO));

        $data = ['order_id' => 2];
        $processed = $strategy->processOrderData($data);
        $this->assertEquals('amazon', $processed['processed_by_strategy']);
    }

    public function testEbayStrategy(): void
    {
        $strategy = new EbayStrategy();
        $this->assertTrue($strategy->supports(Marketplace::EBAY));
        $this->assertFalse($strategy->supports(Marketplace::ALLEGRO));

        $data = ['order_id' => 3];
        $processed = $strategy->processOrderData($data);
        $this->assertEquals('ebay', $processed['processed_by_strategy']);
    }
}
