<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Baselinker;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsDecorator(BaselinkerClient::class)]
final class LoggingBaselinkerClientDecorator implements BaselinkerClientInterface
{
    public function __construct(
        private readonly BaselinkerClientInterface $inner,
        private readonly LoggerInterface $logger,
        private readonly ?Stopwatch $stopwatch = null
    ) {}

    public function call(string $method, array $params = []): array
    {
        $this->stopwatch?->start('baselinker_api_call');
        $startTime = microtime(true);

        try {
            $content = $this->inner->call($method, $params);

            $duration = microtime(true) - $startTime;
            $this->stopwatch?->stop('baselinker_api_call');

            $this->logger->info(sprintf('Baselinker API call: %s', $method), [
                'duration_ms' => $duration * 1000,
                'status' => $content['status'] ?? 'unknown',
            ]);

            return $content;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Baselinker API error: %s', $e->getMessage()), [
                'method' => $method,
                'params' => $params,
            ]);

            throw $e;
        }
    }
}
