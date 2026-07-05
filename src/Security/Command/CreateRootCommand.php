<?php

namespace App\Security\Command;

use App\Security\Service\RegistrationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:root',
    description: 'Создать нового суперпользователя',
)]
class CreateRootCommand extends Command
{
    public function __construct(
        private RegistrationService $registrationService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'm', InputOption::VALUE_OPTIONAL, 'Email')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Пароль')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $io->ask('Введите email: ', null, function ($value) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Неверный формат email');
            }
            return $value;
        });

        $question = new Question('Введите пароль: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($value) {
            if (strlen($value) < 4) {
                throw new \RuntimeException('Пароль не должен быть меньше 8 символов');
            }
            return $value;
        });
        $password = $io->askQuestion($question);

        $confirmQuestion = new Question('Подтвердите пароль: ');
        $confirmQuestion->setHidden(true);
        $confirmQuestion->setHiddenFallback(false);
        $confirmPassword = $io->askQuestion($confirmQuestion);

        if ($password !== $confirmPassword) {
            $io->error('Пароли не совпадают!');
            return Command::FAILURE;
        }

        $this->registrationService->createRootUser($email, $password);

        $io->success([
            'Супер! Регистрация успешна!',
            'Не забудьте поменять пароль после входа!',
        ]);

        return Command::SUCCESS;
    }
}