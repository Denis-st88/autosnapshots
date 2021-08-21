<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use function DI\value;

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

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new \DomainException('Token is invalid.');
        }

        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Token is expired.');
        }
    }

    public function isEqualTo(string $value): bool
    {
        return $this->_value === $value;
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->_expires <= $date;
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
