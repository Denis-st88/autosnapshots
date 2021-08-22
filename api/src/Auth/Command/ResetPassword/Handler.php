<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\Tokenizer;
use DateTimeImmutable;

class Handler
{
    private UserRepository $_users;
    private Tokenizer $_tokenizer;
    private Flusher $_flusher;
    private PasswordResetTokenSender $_sender;

    public function __construct(
        UserRepository $users,
        Tokenizer $tokenizer,
        Flusher $flusher,
        PasswordResetTokenSender $sender
    )
    {
        $this->_users = $users;
        $this->_tokenizer = $tokenizer;
        $this->_flusher = $flusher;
        $this->_sender = $sender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        $user = $this->_users->getByEmail($email);

        $date = new DateTimeImmutable();

        $user->requestPasswordReset(
            $token = $this->_tokenizer->generate($date),
            $date
        );

        $this->_flusher->flush();
        $this->_sender->send($email, $token);
    }
}
