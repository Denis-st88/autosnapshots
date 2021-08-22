<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use DomainException;
use DateTimeImmutable;
use App\Auth\Service\PasswordHasher;
use App\Auth\Entity\User\UserRepository;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByPasswordResetToken($command->token)) {
            throw new DomainException('Token is not found.');
        }

        /* @TODO Как защититься от многократного вызова опрераций востановления пароля.
         * password_hash много потребляет ресурсов компьютера (было сделанно специально, доп. защита).
         * Если у всего приложения есть rateLimiter или failToBan (при ошибках блокируют частые запросы)
         * можно передавать сразу  $this->hasher->hash($command->password).
         * Альтернатива передавть отдельно $command->password и $this->hasher и при прохождении проверок вызывать
         * $this->hasher->hash($command->password) уже внутри метода $user->resetPassword
         */
        $user->resetPassword(
            $command->token,
            new DateTimeImmutable(),
            $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}
