<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;

/**
 * @covers User
 */
class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());

        $user->resetPassword($token->getValue(), $now, $hash = 'hash');

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is invalid.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is expired.');
        $user->resetPassword($token->getValue(), $now->modify('+1 day'), 'hash');
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date
        );
    }
}
