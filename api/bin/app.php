#!/usr/bin/env php
<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

require __DIR__ . '/../vendor/autoload.php';

if (getenv('SENTRY_DSN')) {
    Sentry\init(['dsn' => getenv('SENTRY_DSN')]);
}

/** @var Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console');

if (getenv('SENTRY_DSN')) {
    $cli->setCatchExceptions(false);
}

/**
 * @var string[] $commands
 * @psalm-suppress MixedArrayAccess
 */
$commands = $container->get('config')['console']['commands'];

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

foreach ($commands as $name) {
    /** @var Symfony\Component\Console\Command\Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();
