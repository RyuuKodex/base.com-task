<?php

declare(strict_types=1);

namespace App\Order\Application\Query;

use App\Order\Application\DTO\OrderDTO;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderProduct;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetOrdersHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @return OrderDTO[]
     */
    public function __invoke(GetOrdersQuery $query): array
    {
        $orders = $this->orderRepository->findByFilters(
            $query->marketplace,
            $query->limit,
            $query->offset
        );

        return array_map(fn (Order $order) => new OrderDTO(
            $order->getId()->toString(),
            $order->getExternalId(),
            $order->getMarketplace()->value,
            $order->getCustomerName(),
            $order->getTotalAmount(),
            $order->getCurrency(),
            $order->getCreatedAt()->getTimestamp(),
            array_map(fn (OrderProduct $product) => [
                'order_product_id' => $product->getExternalId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'ean' => $product->getEan(),
                'price' => $product->getPriceBrutto(),
                'quantity' => $product->getQuantity(),
            ], $order->getProducts()->toArray()),
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
        ), $orders);
    }
}
