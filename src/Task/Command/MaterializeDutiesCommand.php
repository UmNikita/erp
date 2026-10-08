<?php

namespace App\Task\Command;

use App\Task\Service\DutyScheduleMaterializer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:tasks:materialize-duties', description: 'Create due duty shifts and their tasks from recurring schedules')]
final class MaterializeDutiesCommand extends Command
{
    public function __construct(private readonly DutyScheduleMaterializer $materializer) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->materializer->materializeUntilToday();
        $output->writeln('Duty schedules synchronized.');
        return Command::SUCCESS;
    }
}
