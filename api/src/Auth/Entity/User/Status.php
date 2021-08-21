<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';

    private string $_name;

    private function __construct(string $name)
    {
        $this->_name = $name;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isWait(): bool
    {
        return $this->_name === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->_name === self::ACTIVE;
    }
}
