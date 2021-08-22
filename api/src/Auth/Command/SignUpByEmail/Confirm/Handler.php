<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByEmail\Confirm;

use DomainException;
use DateTimeImmutable;
use App\Auth\Entity\User\UserRepository;

class Handler
{
    private Flusher $flusher;
    private UserRepository $users;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByConfirmToken($command->token)) {
            throw new DomainException('Incorrect token.');
        }

        $user->confirmSignUp($command->token, new DateTimeImmutable());
        $this->flusher->flush();
    }
}
