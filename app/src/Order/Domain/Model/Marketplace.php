<?php

declare(strict_types=1);

namespace App\Order\Domain\Model;

enum Marketplace: string
{
    case ALLEGRO = 'allegro';
    case EBAY = 'ebay';
    case AMAZON = 'amazon';
    case OTHER = 'other';

    public static function fromSource(string $source): self
    {
        return match (strtolower($source)) {
            'allegro' => self::ALLEGRO,
            'ebay' => self::EBAY,
            'amazon' => self::AMAZON,
            default => self::OTHER,
        };
    }
}
