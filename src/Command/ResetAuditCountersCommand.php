<?php

declare(strict_types=1);

namespace App\Command;

use App\Audit\Infrastructure\AuditLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reset-audit-counters',
    description: 'Reset all SEO and GEO audit logs and counters to 0.'
)]
final class ResetAuditCountersCommand extends Command
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $deleted = $this->auditLogger->clearAll();
        $io->success(sprintf('Reset audit counters: %d audit log file(s) removed.', $deleted));

        return Command::SUCCESS;
    }
}
