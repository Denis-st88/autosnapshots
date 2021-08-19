<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;

class User
{
    private Id $_id;
    private DateTimeImmutable $_date;
    private Email $_email;
    private string $_passwordHash;
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
