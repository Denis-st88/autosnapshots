<?php

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $_id;
    private Email $_email;
    private string $_passwordHash;
    private DateTimeImmutable $_date;
    private Token $_signUpConfirmToken;
    private bool $_active = false;

    public function __construct()
    {
        $this->_id = Id::generate();
        $this->_email = new Email('mail@example.com');
        $this->_passwordHash = 'hash';
        $this->_date = new DateTimeImmutable();
        $this->_signUpConfirmToken = new Token(Uuid::uuid4()->toString(), $this->_date->modify('+1 day'));
    }

    public function withSignUpConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->_signUpConfirmToken = $token;
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->_active = true;
        return $clone;
    }

    public function build(): User
    {
        $user = User::requestSignUpByEmail(
            $this->_id,
            $this->_date,
            $this->_email,
            $this->_passwordHash,
            $this->_signUpConfirmToken
        );

        if ($this->_active) {
            $user->confirmSignUp(
                $this->_signUpConfirmToken->getValue(),
                $this->_signUpConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}
