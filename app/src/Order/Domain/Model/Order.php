<?php

declare(strict_types=1);

namespace App\Order\Domain\Model;

use App\Order\Domain\Event\OrderCreatedEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
final class Order
{
    /** @var array<int, object> */
    private array $domainEvents = [];

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $externalId;

    #[ORM\Column(type: 'string', length: 50, enumType: Marketplace::class)]
    private Marketplace $marketplace;

    #[ORM\Column(type: 'string', length: 255)]
    private string $customerName;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalAmount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $email;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $paymentMethod;

    #[ORM\Column(type: 'boolean')]
    private bool $paid;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $deliveryMethod;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $deliveryPrice;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $userComments;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $adminComments;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $userLogin;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $externalSourceOrderId;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $deliveryFullname;

    #[ORM\Column(type: 'string', length: 156, nullable: true)]
    private ?string $deliveryAddress;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $deliveryPostcode;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $deliveryCity;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private ?string $deliveryCountryCode;

    /** @var Collection<int, OrderProduct> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $products;

    public function __construct(
        OrderId $id,
        int $externalId,
        Marketplace $marketplace,
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
        ?string $deliveryCountryCode = null
    ) {
        $this->id = $id->toString();
        $this->externalId = $externalId;
        $this->marketplace = $marketplace;
        $this->customerName = $customerName;
        $this->totalAmount = $totalAmount;
        $this->currency = $currency;
        $this->createdAt = $createdAt;
        $this->email = $email;
        $this->phone = $phone;
        $this->paymentMethod = $paymentMethod;
        $this->paid = $paid;
        $this->deliveryMethod = $deliveryMethod;
        $this->deliveryPrice = $deliveryPrice;
        $this->userComments = $userComments;
        $this->adminComments = $adminComments;
        $this->userLogin = $userLogin;
        $this->externalSourceOrderId = $externalSourceOrderId;
        $this->deliveryFullname = $deliveryFullname;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryPostcode = $deliveryPostcode;
        $this->deliveryCity = $deliveryCity;
        $this->deliveryCountryCode = $deliveryCountryCode;
        $this->products = new ArrayCollection();

        $this->record(new OrderCreatedEvent($id, $externalId));
    }

    public function addProduct(int $externalId, string $name, ?string $sku, ?string $ean, string $priceBrutto, int $quantity): void
    {
        $this->products->add(new OrderProduct($this, $externalId, $name, $sku, $ean, $priceBrutto, $quantity));
    }

    public function clearProducts(): void
    {
        $this->products->clear();
    }

    public function update(
        string $customerName,
        string $totalAmount,
        string $currency,
        ?string $email,
        ?string $phone,
        ?string $paymentMethod,
        bool $paid,
        ?string $deliveryMethod,
        string $deliveryPrice,
        ?string $userComments,
        ?string $adminComments,
        ?string $userLogin,
        ?string $externalSourceOrderId,
        ?string $deliveryFullname,
        ?string $deliveryAddress,
        ?string $deliveryPostcode,
        ?string $deliveryCity,
        ?string $deliveryCountryCode
    ): void {
        $this->customerName = $customerName;
        $this->totalAmount = $totalAmount;
        $this->currency = $currency;
        $this->email = $email;
        $this->phone = $phone;
        $this->paymentMethod = $paymentMethod;
        $this->paid = $paid;
        $this->deliveryMethod = $deliveryMethod;
        $this->deliveryPrice = $deliveryPrice;
        $this->userComments = $userComments;
        $this->adminComments = $adminComments;
        $this->userLogin = $userLogin;
        $this->externalSourceOrderId = $externalSourceOrderId;
        $this->deliveryFullname = $deliveryFullname;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryPostcode = $deliveryPostcode;
        $this->deliveryCity = $deliveryCity;
        $this->deliveryCountryCode = $deliveryCountryCode;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return array<int, object>
     */
    public function pullEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function getId(): OrderId
    {
        return new OrderId($this->id);
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function getMarketplace(): Marketplace
    {
        return $this->marketplace;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function getDeliveryMethod(): ?string
    {
        return $this->deliveryMethod;
    }

    public function getDeliveryPrice(): string
    {
        return $this->deliveryPrice;
    }

    public function getUserComments(): ?string
    {
        return $this->userComments;
    }

    public function getAdminComments(): ?string
    {
        return $this->adminComments;
    }

    public function getUserLogin(): ?string
    {
        return $this->userLogin;
    }

    public function getExternalSourceOrderId(): ?string
    {
        return $this->externalSourceOrderId;
    }

    public function getDeliveryFullname(): ?string
    {
        return $this->deliveryFullname;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function getDeliveryPostcode(): ?string
    {
        return $this->deliveryPostcode;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function getDeliveryCountryCode(): ?string
    {
        return $this->deliveryCountryCode;
    }
}
