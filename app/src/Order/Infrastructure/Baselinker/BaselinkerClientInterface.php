<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Baselinker;

interface BaselinkerClientInterface
{
    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function call(string $method, array $params = []): array;
}
