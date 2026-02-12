<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Messenger;

use App\Order\Application\Command\ProcessOrderCommand;
use App\Order\Application\Command\SynchronizeOrdersCommand;
use App\Order\Application\DTO\OrderDTO;
use App\Order\Domain\Repository\BaselinkerOrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class SynchronizeOrdersHandler
{
    public function __construct(
        private BaselinkerOrderRepositoryInterface $baselinkerOrderRepository,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger
    ) {}

    public function __invoke(SynchronizeOrdersCommand $command): void
    {
        $this->logger->info('Starting synchronization of all orders');

        $orders = $this->baselinkerOrderRepository->getOrders();

        $this->logger->info(sprintf('Fetched %d orders from Baselinker API.', count($orders)));

        foreach ($orders as $order) {
            $productsData = [];
            foreach ($order->getProducts() as $product) {
                $productsData[] = [
                    'order_product_id' => $product->getExternalId(),
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'ean' => $product->getEan(),
                    'price_brutto' => $product->getPriceBrutto(),
                    'quantity' => $product->getQuantity(),
                ];
            }

            $this->messageBus->dispatch(new ProcessOrderCommand(
                new OrderDTO(
                    null,
                    $order->getExternalId(),
                    $order->getMarketplace()->value,
                    $order->getCustomerName(),
                    $order->getTotalAmount(),
                    $order->getCurrency(),
                    $order->getCreatedAt()->getTimestamp(),
                    $productsData,
                    $order->getEmail(),
                    $order->getPhone(),
                    $order->getPaymentMethod(),
                    $order->isPaid(),
                    $order->getDeliveryMethod(),
                    $order->getDeliveryPrice(),
                    $order->getUserComments(),
                    $order->getAdminComments(),
                    $order->getUserLogin(),
                    $order->getExternalSourceOrderId(),
                    $order->getDeliveryFullname(),
                    $order->getDeliveryAddress(),
                    $order->getDeliveryPostcode(),
                    $order->getDeliveryCity(),
                    $order->getDeliveryCountryCode()
                )
            ));
        }
    }
}
