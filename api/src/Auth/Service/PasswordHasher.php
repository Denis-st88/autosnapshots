<?php

declare(strict_types=1);

namespace App\Auth\Service;

interface PasswordHasher
{
    public function add(string $password): string;
    public function validate(string $password, string $hash): bool;
}
