<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DomainException;
use DateTimeImmutable;

class User
{
    private Id $_id;
    private Email $_email;
    private Status $_status;
    private string $_passwordHash;
    private DateTimeImmutable $_date;
    private ?Token $_signUpConfirmToken;

    public function __construct(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    )
    {
        $this->_id = $id;
        $this->_date = $date;
        $this->_email = $email;
        $this->_passwordHash = $passwordHash;
        $this->_signUpConfirmToken = $token;
        $this->_status = Status::wait();
    }

    public function confirmSignUp(string $token, DateTimeImmutable $date): void
    {
        if ($this->_signUpConfirmToken === null) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->_signUpConfirmToken->validate($token, $date);
        $this->_status = Status::active();
        $this->_signUpConfirmToken = null;
    }

    public function isWait(): bool
    {
        return $this->_status->isWait();
    }

    public function isActive(): bool
    {
        return $this->_status->isActive();
    }

    public function getId(): Id
    {
        return $this->_id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->_date;
    }

    public function getEmail(): Email
    {
        return $this->_email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->_passwordHash;
    }

    public function getSignUpConfirmToken(): ?Token
    {
        return $this->_signUpConfirmToken;
    }
}
