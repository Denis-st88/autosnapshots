<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;
use InvalidArgumentException;

class Email
{
    private string $_value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);
        $this->_value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->_value;
    }
}
