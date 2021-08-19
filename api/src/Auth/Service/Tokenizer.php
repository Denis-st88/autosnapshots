<?php

declare(strict_types=1);

namespace App\Auth\Service;

use DateTimeImmutable;
use App\Auth\Entity\User\Token;

interface Tokenizer
{
    public function generate(DateTimeImmutable $date): Token;
}
