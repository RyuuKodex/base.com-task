<?php

declare(strict_types=1);

namespace App\Tests\functional;

use App\Tests\functional\helper\HttpClientAwareTestCase;

final class OrderControllerTest extends HttpClientAwareTestCase
{
    public function testGetOrdersList(): void
    {
        $response = $this->httpClient->request('GET', '/api/orders');

        self::assertSame(200, $response->getStatusCode());
        self::assertIsArray($response->toArray());
    }

    public function testGetOrdersListWithMarketplaceFilter(): void
    {
        $response = $this->httpClient->request('GET', '/api/orders?marketplace=allegro');

        self::assertSame(200, $response->getStatusCode());
        $data = $response->toArray();
        self::assertIsArray($data);

        foreach ($data as $order) {
            self::assertSame('allegro', $order['marketplace']);
        }
    }
}
