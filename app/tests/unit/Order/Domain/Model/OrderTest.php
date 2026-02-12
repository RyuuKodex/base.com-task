<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Model;

use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderId;
use App\Order\Domain\Model\OrderProduct;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function testOrderCreation(): void
    {
        $orderId = OrderId::generate();
        $externalId = 12345;
        $marketplace = Marketplace::ALLEGRO;
        $customerName = 'Jan Kowalski';
        $totalAmount = '150.00';
        $currency = 'PLN';
        $createdAt = new \DateTimeImmutable();

        $order = new Order(
            $orderId,
            $externalId,
            $marketplace,
            $customerName,
            $totalAmount,
            $currency,
            $createdAt
        );

        $this->assertEquals($orderId, $order->getId());
        $this->assertEquals($externalId, $order->getExternalId());
        $this->assertEquals($marketplace, $order->getMarketplace());
        $this->assertEquals($customerName, $order->getCustomerName());
        $this->assertEquals($totalAmount, $order->getTotalAmount());
        $this->assertEquals($currency, $order->getCurrency());
        $this->assertEquals($createdAt, $order->getCreatedAt());
    }

    public function testOrderProducts(): void
    {
        $order = new Order(
            OrderId::generate(),
            123,
            Marketplace::ALLEGRO,
            'Customer',
            '100.00',
            'PLN',
            new \DateTimeImmutable()
        );

        $this->assertCount(0, $order->getProducts());

        $order->addProduct(1, 'Product 1', 'SKU1', 'EAN1', '50.00', 2);

        $this->assertCount(1, $order->getProducts());
        $product = $order->getProducts()->first();
        $this->assertInstanceOf(OrderProduct::class, $product);
        $this->assertEquals('Product 1', $product->getName());
        $this->assertEquals(1, $product->getExternalId());
        $this->assertEquals('50.00', $product->getPriceBrutto());

        $order->clearProducts();
        $this->assertCount(0, $order->getProducts());
    }
}
