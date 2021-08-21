<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByNetwork;

use DomainException;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Entity\User\NetworkIdentity;

class Handler
{
    private UserRepository $_users;
    private Flusher $_flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->_users = $users;
        $this->_flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $identity = new NetworkIdentity($command->network, $command->identity);
        $email = new Email($command->email);

        if ($this->_users->hasByNetwork($identity)) {
            throw new DomainException('User with this network already exists.');
        }

        if ($this->_users->hasByEmail($email)) {
            throw new DomainException('User with this email already exists.');
        }

        $user = User::signUpByNetwork(
            Id::generate(),
            new DateTimeImmutable(),
            $email,
            $identity
        );

        $this->_users->add($user);
        $this->_flusher->flush();
    }
}
