<?php

declare(strict_types=1);

namespace App\Console;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\SignUpConfirmationSender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailCheckCommand extends Command
{
    private SignUpConfirmationSender $sender;

    public function __construct(SignUpConfirmationSender $sender)
    {
        parent::__construct();
        $this->sender = $sender;
    }

    protected function configure(): void
    {
        $this->setName('mailer:check');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<commen>Sending</commen>');

        $this->sender->send(
            new Email('user@app.test'),
            new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable())
        );

        $output->writeln('<info>Done!</info>');

        return 0;
    }
}
