<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    private string $_value;

    public function __construct(string $value)
    {
        Assert::uuid($value);
        $this->_value = mb_strtolower($value);
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->_value;
    }
}
