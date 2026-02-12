<?php

declare(strict_types=1);

namespace App\Order\Domain\Model;

use Symfony\Component\Uid\Uuid;

final readonly class OrderId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(sprintf('Invalid Order ID format: %s. Expected UUID.', $value));
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toString());
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
