<?php

declare(strict_types=1);

namespace App\Order\Application\DTO;

final readonly class OrderDTO
{
    public function __construct(
        public ?string $id,
        public int $externalId,
        public string $marketplace,
        public string $customerName,
        public string $totalAmount,
        public string $currency,
        public int $createdAtTimestamp,
        /** @var array<int, array<string, mixed>> */
        public array $products,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $paymentMethod = null,
        public bool $paid = false,
        public ?string $deliveryMethod = null,
        public string $deliveryPrice = '0.00',
        public ?string $userComments = null,
        public ?string $adminComments = null,
        public ?string $userLogin = null,
        public ?string $externalSourceOrderId = null,
        public ?string $deliveryFullname = null,
        public ?string $deliveryAddress = null,
        public ?string $deliveryPostcode = null,
        public ?string $deliveryCity = null,
        public ?string $deliveryCountryCode = null
    ) {}
}
