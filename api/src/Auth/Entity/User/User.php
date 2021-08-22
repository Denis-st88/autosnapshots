<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use ArrayObject;
use DomainException;
use DateTimeImmutable;

class User /* @TODO спам и безопасность */
{
    private Id $id;
    private Email $email;
    private Status $status;
    private ?string $passwordHash = null;
    private DateTimeImmutable $date;
    private ?Token $signUpConfirmToken = null;
    private ArrayObject $networks;
    private ?Token $passwordResetToken = null;

    public function __construct(Id $id, DateTimeImmutable $date, Email $email, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->networks = new ArrayObject();
    }

    public static function requestSignUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->signUpConfirmToken = $token;
        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->append($identity);
        return $user;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var NetworkIdentity $existing $existing */
        foreach ($this->networks as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new DomainException('Network already attached.');
            }
        }
        $this->networks->append($identity);
    }

    public function confirmSignUp(string $token, DateTimeImmutable $date): void
    {
        if ($this->signUpConfirmToken === null) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->signUpConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->signUpConfirmToken = null;
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if ($this->passwordResetToken === null) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getSignUpConfirmToken(): ?Token
    {
        return $this->signUpConfirmToken;
    }

    /**
     * @return NetworkIdentity[]
     */
    public function getNetworks(): array
    {
        /** @var NetworkIdentity[] */
        return $this->networks->getArrayCopy();
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }
}
