<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Baselinker;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BaselinkerClient implements BaselinkerClientInterface
{
    private const API_URL = 'https://api.baselinker.com/connector.php';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiToken
    ) {}

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function call(string $method, array $params = []): array
    {
        $response = $this->httpClient->request('POST', self::API_URL, [
            'headers' => [
                'X-BLToken' => $this->apiToken,
            ],
            'body' => [
                'method' => $method,
                'parameters' => json_encode($params),
            ],
        ]);

        $content = $response->toArray();

        if (($content['status'] ?? '') === 'ERROR') {
            throw new \Exception($content['error_message'] ?? 'Unknown Baselinker API error');
        }

        return $content;
    }
}
