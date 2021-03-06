<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class UserFixture extends AbstractFixture
{
    private const PASSWORD_HASH = '$2y$12$qwnND33o8DGWvFoepotSju7eTAQ6gzLD/zy6W8NCVtiHPbkybz.w6';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestSignUpByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new DateTimeImmutable('-30 days'),
            new Email('user@app.test'),
            self::PASSWORD_HASH,
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );

        $user->confirmSignUp($value, $date);

        $manager->persist($user);

        $manager->flush();
    }
}
