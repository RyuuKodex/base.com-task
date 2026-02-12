<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Infrastructure\Baselinker;

use App\Order\Domain\Factory\OrderFactory;
use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Strategy\DefaultMarketplaceStrategy;
use App\Order\Domain\Strategy\MarketplaceStrategyRegistry;
use App\Order\Infrastructure\Baselinker\BaselinkerClientInterface;
use App\Order\Infrastructure\Baselinker\BaselinkerOrderRepository;
use PHPUnit\Framework\TestCase;

final class BaselinkerOrderRepositoryTest extends TestCase
{
    public function testGetOrders(): void
    {
        $client = $this->createMock(BaselinkerClientInterface::class);
        $orderFactory = new OrderFactory();

        $defaultStrategy = new DefaultMarketplaceStrategy();
        $registry = new MarketplaceStrategyRegistry([$defaultStrategy]);

        $client->expects($this->exactly(2))
            ->method('call')
            ->willReturnMap([
                ['getOrders', ['get_unconfirmed_orders' => true], [
                    'status' => 'SUCCESS',
                    'orders' => [
                        ['order_id' => 101, 'order_source' => 'allegro'],
                    ],
                ]],
                ['getOrders', ['order_id' => 101], [
                    'status' => 'SUCCESS',
                    'orders' => [
                        [
                            'order_id' => 101,
                            'order_source' => 'allegro',
                            'delivery_fullname' => 'John Doe',
                            'total_price' => '150.00',
                            'currency' => 'PLN',
                            'date_add' => time(),
                            'products' => [],
                        ],
                    ],
                ]],
            ])
        ;

        $repository = new BaselinkerOrderRepository($client, $orderFactory, $registry);
        $orders = $repository->getOrders();

        $this->assertCount(1, $orders);
        $this->assertEquals(101, $orders[0]->getExternalId());
        $this->assertEquals(Marketplace::ALLEGRO, $orders[0]->getMarketplace());
    }
}
