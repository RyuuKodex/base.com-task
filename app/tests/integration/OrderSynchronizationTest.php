<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Order\Application\Command\ProcessOrderCommand;
use App\Order\Application\DTO\OrderDTO;
use App\Order\Domain\Factory\OrderFactory;
use App\Order\Domain\Model\Order;
use App\Order\Infrastructure\Messenger\ProcessOrderHandler;
use App\Order\Infrastructure\Persistence\Doctrine\DoctrineOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderSynchronizationTest extends KernelTestCase
{
    public function testOrderProcessingAndSaving(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => false]);
        $container = self::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');

        /** @var LoggerInterface $logger */
        $logger = $container->get('logger');

        $orderRepository = new DoctrineOrderRepository($entityManager);
        $orderFactory = new OrderFactory();

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');

        $handler = new ProcessOrderHandler($orderRepository, $orderFactory, $eventDispatcher, $logger);

        $externalId = 2001;
        $dto = new OrderDTO(
            null,
            $externalId,
            'allegro',
            'Test Customer',
            '250.00',
            'PLN',
            time(),
            [],
            null,
            null,
            null,
            false,
            null,
            '0.00',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $command = new ProcessOrderCommand($dto);

        $handler($command);

        $this->assertTrue($orderRepository->existsByExternalId($externalId));

        /** @var Order $savedOrder */
        $savedOrder = $entityManager->getRepository(Order::class)->findOneBy(['externalId' => $externalId]);
        $this->assertEquals('Test Customer', $savedOrder->getCustomerName());
    }
}
