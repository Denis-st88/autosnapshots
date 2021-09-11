<?php

declare(strict_types=1);

use App\Auth\Entity\User\User;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Auth\Entity\User\UserRepository;

return [
    UserRepository::class => function (ContainerInterface $container): UserRepository {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(User::class);
        return new UserRepository($em, $repo);
    }
];
