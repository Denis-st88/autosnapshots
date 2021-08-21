<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class NetworkIdentity
{
    private string $_network;
    private string $_identity;

    public function __construct(string $network, string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);
        $this->_network = mb_strtolower($network);
        $this->_identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getNetwork() === $network->getNetwork() &&
            $this->getIdentity() === $network->getIdentity();
    }

    public function getNetwork(): string
    {
        return $this->_network;
    }

    public function getIdentity(): string
    {
        return $this->_identity;
    }
}
