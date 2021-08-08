<?php

declare(strict_types=1);

use Slim\App;
use Psr\Container\ContainerInterface;

return static function (App $app, ContainerInterface $container): void {
    $app->addErrorMiddleware($container->get('config')['debug'], true, true);
};
