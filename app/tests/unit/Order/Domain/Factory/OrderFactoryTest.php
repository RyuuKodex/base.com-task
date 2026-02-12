<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Factory;

use App\Order\Domain\Factory\OrderFactory;
use App\Order\Domain\Model\Marketplace;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class OrderFactoryTest extends TestCase
{
    public function testCreateFromExternalData(): void
    {
        $factory = new OrderFactory();
        $order = $factory->createFromExternalData(
            123,
            'allegro',
            'John Doe',
            '100.50',
            'PLN',
            new \DateTimeImmutable('2024-01-01 10:00:00')
        );

        $this->assertEquals(123, $order->getExternalId());
        $this->assertTrue(Uuid::isValid($order->getId()->toString()));
        $this->assertEquals(Marketplace::ALLEGRO, $order->getMarketplace());
        $this->assertEquals('John Doe', $order->getCustomerName());
        $this->assertEquals('100.50', $order->getTotalAmount());
        $this->assertEquals('PLN', $order->getCurrency());
        $this->assertEquals('2024-01-01T10:00:00+00:00', $order->getCreatedAt()->format(\DateTimeInterface::ATOM));
        $this->assertNotEmpty($order->pullEvents());
    }
}
