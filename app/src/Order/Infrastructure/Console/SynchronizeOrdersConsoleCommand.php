<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Console;

use App\Order\Application\Command\SynchronizeOrdersCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:orders:sync',
    description: 'Synchronizes orders from Baselinker API',
)]
final class SynchronizeOrdersConsoleCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Starting full synchronization from Baselinker');

        try {
            $this->messageBus->dispatch(new SynchronizeOrdersCommand());
            $io->success('Synchronization finished successfully.');
        } catch (\Throwable $e) {
            $io->error(sprintf('Synchronization failed: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
