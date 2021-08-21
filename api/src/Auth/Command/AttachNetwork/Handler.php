<?php

declare(strict_types=1);

namespace App\Auth\Command\AttachNetwork;

use DomainException;
use App\Auth\Entity\User\Id;
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

        if ($this->_users->hasByNetwork($identity)) {
            throw new DomainException('User with this network already exists.');
        }

        $user = $this->_users->get(new Id($command->id));
        $user->attachNetwork($identity);
        $this->_flusher->flush();
    }
}
