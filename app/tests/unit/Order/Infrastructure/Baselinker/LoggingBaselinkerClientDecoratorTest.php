<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Infrastructure\Baselinker;

use App\Order\Infrastructure\Baselinker\BaselinkerClientInterface;
use App\Order\Infrastructure\Baselinker\LoggingBaselinkerClientDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final class LoggingBaselinkerClientDecoratorTest extends TestCase
{
    public function testCallLogsInformation(): void
    {
        $inner = $this->createMock(BaselinkerClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $stopwatch = $this->createMock(Stopwatch::class);

        $inner->expects($this->once())
            ->method('call')
            ->with('getOrders', [])
            ->willReturn(['status' => 'SUCCESS'])
        ;

        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Baselinker API call: getOrders'))
        ;

        $decorator = new LoggingBaselinkerClientDecorator($inner, $logger, $stopwatch);
        $decorator->call('getOrders');
    }
}
