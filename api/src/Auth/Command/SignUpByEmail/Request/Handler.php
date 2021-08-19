<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByEmail\Request;

use DomainException;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;

class Handler
{
    private UserRepository $_users;
    private PasswordHasher $_hasher;
    private Tokenizer $_tokenizer;
    private Flusher $_flusher;
    private SignUpConfirmSender $_sender;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Tokenizer $tokenizer,
        Flusher $flusher,
        SignUpConfirmSender $sender
    )
    {
        $this->_users = $users;
        $this->_hasher = $hasher;
        $this->_tokenizer = $tokenizer;
        $this->_flusher = $flusher;
        $this->_sender = $sender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User already exists');
        }

        $date = new DateTimeImmutable();

        $user = new User(
            Id::generate(),
            $date,
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($date)
        );

        $this->_users->add($user);
        $this->_flusher->flush();
        $this->_sender->send($email, $token);
    }
}
