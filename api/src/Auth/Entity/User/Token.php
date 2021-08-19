<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

class Token
{
    private string $_value;
    private DateTimeImmutable $_expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->_value = mb_strtolower($value);
        $this->_expires = $expires;
    }

    public function getValue(): string
    {
        return $this->_value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->_expires;
    }
}
