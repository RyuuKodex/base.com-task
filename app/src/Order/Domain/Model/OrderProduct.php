<?php

declare(strict_types=1);

namespace App\Order\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'order_products')]
final class OrderProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $externalId;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sku;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $ean;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $priceBrutto;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(
        Order $order,
        int $externalId,
        string $name,
        ?string $sku,
        ?string $ean,
        string $priceBrutto,
        int $quantity
    ) {
        $this->id = Uuid::v4()->toString();
        $this->order = $order;
        $this->externalId = $externalId;
        $this->name = $name;
        $this->sku = $sku;
        $this->ean = $ean;
        $this->priceBrutto = $priceBrutto;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getPriceBrutto(): string
    {
        return $this->priceBrutto;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
