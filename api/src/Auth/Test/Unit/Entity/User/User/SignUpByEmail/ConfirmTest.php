<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\SignUpByEmail;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;

/**
 * @covers User
 */
class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getSignUpConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is invalid.');

        $user->confirmSignUp(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is expired.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    public function testAlready(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token)
            ->active()
            ->build();

        $this->expectExceptionMessage('Confirmation is not required.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
    }
}
