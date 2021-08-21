<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\SignUpByEmail;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;

/**
 * @covers User
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
            $hash = 'hash',
            $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getSignUpConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }
}
