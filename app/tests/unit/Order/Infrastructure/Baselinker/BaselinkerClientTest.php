<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Infrastructure\Baselinker;

use App\Order\Infrastructure\Baselinker\BaselinkerClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class BaselinkerClientTest extends TestCase
{
    public function testCallSendsCorrectRequest(): void
    {
        $json = json_encode(['status' => 'SUCCESS', 'orders' => []]);
        if (false === $json) {
            $json = '';
        }
        $mockResponse = new MockResponse($json);
        $httpClient = new MockHttpClient($mockResponse);
        $apiToken = 'test-token';

        $client = new BaselinkerClient($httpClient, $apiToken);
        $client->call('getOrders', ['date_from' => 123456789]);

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.baselinker.com/connector.php', $mockResponse->getRequestUrl());

        $headers = $mockResponse->getRequestOptions()['headers'];
        $this->assertContains('X-BLToken: test-token', $headers);

        $body = (string) ($mockResponse->getRequestOptions()['body'] ?? '');
        $this->assertStringContainsString('method=getOrders', $body);
        $paramsJson = json_encode(['date_from' => 123456789]);
        if (false === $paramsJson) {
            $paramsJson = '';
        }
        $this->assertStringContainsString('parameters='.urlencode($paramsJson), $body);
        $this->assertStringNotContainsString('token=', $body);
    }
}
