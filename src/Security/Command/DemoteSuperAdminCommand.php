<?php

namespace App\Security\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:demote-super-admin',
    description: 'Создать нового суперпользователя',
)]
class DemoteSuperAdminCommand  extends Command
{
    public function __construct() {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'Email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $id = $io->ask('Введите id суперпользователя: ', null, function ($value) {
            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                throw new \RuntimeException('Неверный формат id');
            }
            return $value;
        });

        $io->success([
            'Пользователь лишен привелегий суперпользователя!',
        ]);

        return Command::SUCCESS;
    }
}