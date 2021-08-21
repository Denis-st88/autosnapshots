<?php

declare(strict_types=1);

namespace App\Auth\Test\Service;

use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Auth\Service\Tokenizer;

/**
 * @covers Tokenizer
 */
class TokenizerTest extends TestCase
{
    public function testSuccess()
    {
        $interval = new DateInterval('PT1H');
        $date = new DateTimeImmutable('+1 day');

        $tokenizer = new Tokenizer($interval);
        $token = $tokenizer->generate($date);

        self::assertEquals($date->add($interval), $token->getExpires());
    }
}
