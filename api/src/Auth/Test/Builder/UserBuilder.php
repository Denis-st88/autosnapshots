<?php

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $passwordHash;
    private DateTimeImmutable $date;
    private Token $signUpConfirmToken;
    private bool $active = false;
    private ?Network $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('mail@example.com');
        $this->passwordHash = 'hash';
        $this->date = new DateTimeImmutable();
        $this->signUpConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withSignUpConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->signUpConfirmToken = $token;
        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function viaNetwork(Network $identity = null): self
    {
        $clone = clone $this;
        $clone->networkIdentity = $identity ?? new Network('vk', '0000001');
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity !== null) {
            return User::signUpByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity
            );
        }

        $user = User::requestSignUpByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->passwordHash,
            $this->signUpConfirmToken
        );

        if ($this->active) {
            $user->confirmSignUp(
                $this->signUpConfirmToken->getValue(),
                $this->signUpConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}
