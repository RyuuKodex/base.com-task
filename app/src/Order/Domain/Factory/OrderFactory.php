<?php

declare(strict_types=1);

namespace App\Order\Domain\Factory;

use App\Order\Domain\Model\Marketplace;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderId;

final class OrderFactory
{
    /**
     * @param array<int, array<string, mixed>> $products
     */
    public function createFromExternalData(
        int $externalId,
        string $marketplaceSource,
        string $customerName,
        string $totalAmount,
        string $currency,
        \DateTimeImmutable $createdAt,
        ?string $email = null,
        ?string $phone = null,
        ?string $paymentMethod = null,
        bool $paid = false,
        ?string $deliveryMethod = null,
        string $deliveryPrice = '0.00',
        ?string $userComments = null,
        ?string $adminComments = null,
        ?string $userLogin = null,
        ?string $externalSourceOrderId = null,
        ?string $deliveryFullname = null,
        ?string $deliveryAddress = null,
        ?string $deliveryPostcode = null,
        ?string $deliveryCity = null,
        ?string $deliveryCountryCode = null,
        array $products = []
    ): Order {
        $order = new Order(
            OrderId::generate(),
            $externalId,
            Marketplace::fromSource($marketplaceSource),
            $customerName,
            $totalAmount,
            $currency,
            $createdAt,
            $email,
            $phone,
            $paymentMethod,
            $paid,
            $deliveryMethod,
            $deliveryPrice,
            $userComments,
            $adminComments,
            $userLogin,
            $externalSourceOrderId,
            $deliveryFullname,
            $deliveryAddress,
            $deliveryPostcode,
            $deliveryCity,
            $deliveryCountryCode
        );

        foreach ($products as $productData) {
            $order->addProduct(
                (int) ($productData['order_product_id'] ?? 0),
                $productData['name'] ?? 'Unknown',
                $productData['sku'] ?? null,
                $productData['ean'] ?? null,
                (string) ($productData['price_brutto'] ?? '0.00'),
                (int) ($productData['quantity'] ?? 1)
            );
        }

        return $order;
    }
}
