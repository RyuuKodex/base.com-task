<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Messenger;

use App\Order\Application\Command\ProcessOrderCommand;
use App\Order\Domain\Factory\OrderFactory;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderFactory $orderFactory,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ProcessOrderCommand $command): void
    {
        $dto = $command->orderDTO;

        $existingOrder = $this->orderRepository->findByExternalId($dto->externalId);

        try {
            if ($existingOrder) {
                $this->logger->info(sprintf('Order %d already exists, updating.', $dto->externalId));

                $existingOrder->update(
                    $dto->customerName,
                    $dto->totalAmount,
                    $dto->currency,
                    $dto->email,
                    $dto->phone,
                    $dto->paymentMethod,
                    $dto->paid,
                    $dto->deliveryMethod,
                    $dto->deliveryPrice,
                    $dto->userComments,
                    $dto->adminComments,
                    $dto->userLogin,
                    $dto->externalSourceOrderId,
                    $dto->deliveryFullname,
                    $dto->deliveryAddress,
                    $dto->deliveryPostcode,
                    $dto->deliveryCity,
                    $dto->deliveryCountryCode
                );

                $existingOrder->clearProducts();
                $this->orderRepository->save($existingOrder);

                foreach ($dto->products as $productData) {
                    $existingOrder->addProduct(
                        (int) ($productData['order_product_id'] ?? 0),
                        $productData['name'] ?? 'Unknown',
                        $productData['sku'] ?? null,
                        $productData['ean'] ?? null,
                        (string) ($productData['price_brutto'] ?? '0.00'),
                        (int) ($productData['quantity'] ?? 1)
                    );
                }

                $this->orderRepository->save($existingOrder);

                return;
            }

            $order = $this->orderFactory->createFromExternalData(
                $dto->externalId,
                $dto->marketplace,
                $dto->customerName,
                $dto->totalAmount,
                $dto->currency,
                new \DateTimeImmutable('@'.$dto->createdAtTimestamp),
                $dto->email,
                $dto->phone,
                $dto->paymentMethod,
                $dto->paid,
                $dto->deliveryMethod,
                $dto->deliveryPrice,
                $dto->userComments,
                $dto->adminComments,
                $dto->userLogin,
                $dto->externalSourceOrderId,
                $dto->deliveryFullname,
                $dto->deliveryAddress,
                $dto->deliveryPostcode,
                $dto->deliveryCity,
                $dto->deliveryCountryCode,
                $dto->products
            );

            $this->orderRepository->save($order);

            foreach ($order->pullEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }

            $this->logger->info(sprintf('Order %d processed successfully.', $dto->externalId));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error processing order %d: %s', $dto->externalId, $e->getMessage()));

            throw $e;
        }
    }
}
