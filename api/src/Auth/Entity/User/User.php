<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use ArrayObject;
use DomainException;
use DateTimeImmutable;

class User
{
    private Id $_id;
    private Email $_email;
    private Status $_status;
    private ?string $_passwordHash = null;
    private DateTimeImmutable $_date;
    private ?Token $_signUpConfirmToken = null;
    private ArrayObject $_networks;
    private ?Token $_passwordResetToken = null;

    public function __construct(Id $id, DateTimeImmutable $date, Email $email, Status $status)
    {
        $this->_id = $id;
        $this->_date = $date;
        $this->_email = $email;
        $this->_status = $status;
        $this->_networks = new ArrayObject();
    }

    public static function requestSignUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->_passwordHash = $passwordHash;
        $user->_signUpConfirmToken = $token;
        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new self($id, $date, $email, Status::active());
        $user->_networks->append($identity);
        return $user;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var NetworkIdentity $existing $existing */
        foreach ($this->_networks as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new DomainException('Network already attached.');
            }
        }
        $this->_networks->append($identity);
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

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->_passwordResetToken !== null && !$this->_passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->_passwordResetToken = $token;
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

    /**
     * @return NetworkIdentity[]
     */
    public function getNetworks(): array
    {
        /** @var NetworkIdentity[] */
        return $this->_networks->getArrayCopy();
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->_passwordResetToken;
    }
}
