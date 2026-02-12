<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Baselinker;

use App\Order\Domain\Factory\OrderFactory;
use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Repository\BaselinkerOrderRepositoryInterface;
use App\Order\Domain\Strategy\MarketplaceStrategyRegistry;

final class BaselinkerOrderRepository implements BaselinkerOrderRepositoryInterface
{
    public function __construct(
        private readonly BaselinkerClientInterface $client,
        private readonly OrderFactory $orderFactory,
        private readonly MarketplaceStrategyRegistry $strategyRegistry
    ) {}

    /**
     * @return Order[]
     *
     * @throws \Exception
     */
    public function getOrders(): array
    {
        $response = $this->client->call('getOrders', [
            'get_unconfirmed_orders' => true,
        ]);

        $ordersData = $response['orders'] ?? [];
        if (empty($ordersData)) {
            return [];
        }

        $orderIds = array_map(fn ($o) => (int) $o['order_id'], $ordersData);

        $fullOrdersData = [];
        foreach ($orderIds as $orderId) {
            $detailsResponse = $this->client->call('getOrders', [
                'order_id' => $orderId,
            ]);

            foreach ($detailsResponse['orders'] ?? [] as $fullOrder) {
                $fullOrdersData[$fullOrder['order_id']] = $fullOrder;
            }
        }

        $orders = [];
        foreach ($ordersData as $orderData) {
            $orderId = (int) $orderData['order_id'];
            $fullOrderData = $fullOrdersData[$orderId] ?? $orderData;

            $marketplace = Marketplace::fromSource($fullOrderData['order_source'] ?? 'other');
            $strategy = $this->strategyRegistry->getStrategy($marketplace);
            $processedData = $strategy->processOrderData($fullOrderData);

            $orders[] = $this->orderFactory->createFromExternalData(
                (int) $processedData['order_id'],
                $processedData['order_source'] ?? 'other',
                $processedData['delivery_fullname'] ?? $processedData['invoice_fullname'] ?? 'Unknown',
                (string) ($processedData['total_price'] ?? '0.00'),
                $processedData['currency'] ?? 'PLN',
                new \DateTimeImmutable('@'.($processedData['date_add'] ?? time())),
                $processedData['email'] ?? null,
                $processedData['phone'] ?? null,
                $processedData['payment_method'] ?? null,
                ($processedData['payment_done'] ?? 0) >= ($processedData['total_price'] ?? 0),
                $processedData['delivery_method'] ?? null,
                (string) ($processedData['delivery_price'] ?? '0.00'),
                $processedData['user_comments'] ?? null,
                $processedData['admin_comments'] ?? null,
                $processedData['user_login'] ?? null,
                $processedData['external_order_id'] ?? null,
                $processedData['delivery_fullname'] ?? null,
                $processedData['delivery_address'] ?? null,
                $processedData['delivery_postcode'] ?? null,
                $processedData['delivery_city'] ?? null,
                $processedData['delivery_country_code'] ?? null,
                $processedData['products'] ?? []
            );
        }

        return $orders;
    }
}
