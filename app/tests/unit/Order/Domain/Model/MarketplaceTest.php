<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Domain\Model;

use App\Order\Domain\Model\Marketplace;
use PHPUnit\Framework\TestCase;

final class MarketplaceTest extends TestCase
{
    /**
     * @dataProvider marketplaceSourceProvider
     */
    public function testFromSource(string $source, Marketplace $expected): void
    {
        $this->assertEquals($expected, Marketplace::fromSource($source));
    }

    /**
     * @return array<int, array{0: string, 1: Marketplace}>
     */
    public function marketplaceSourceProvider(): array
    {
        return [
            ['allegro', Marketplace::ALLEGRO],
            ['Allegro', Marketplace::ALLEGRO],
            ['ebay', Marketplace::EBAY],
            ['AMAZON', Marketplace::AMAZON],
            ['unknown', Marketplace::OTHER],
            ['shop', Marketplace::OTHER],
        ];
    }
}
