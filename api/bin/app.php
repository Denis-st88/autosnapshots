#!/usr/bin/env php
<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

/** @var Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console');

/**
 * @var string[] $commands
 * @psalm-suppress MixedArrayAccess
 */
$commands = $container->get('config')['console']['commands'];

foreach ($commands as $name) {
    /** @var Symfony\Component\Console\Command\Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();
