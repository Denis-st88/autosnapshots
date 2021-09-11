<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

return [
    LoggerInterface::class => function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var  array{
         *     debug:bool,
         *     file:string,
         *     stderr:bool
         * } $config
         */
        $config = $container->get('config')['logger'];

        $level = $config['debug'] ? Logger::DEBUG : Logger::ERROR;

        $log = new Logger('API');

        if ($config['stderr']) {
            $log->pushHandler(new StreamHandler('php://stderr', $level));
        }

        if (!empty($config['file'])) {
            $log->pushHandler(new StreamHandler($config['file'], $level));
        }

        return $log;
    },

    'config' => [
        'logger' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'file' => null,
            'stderr' => true
        ]
    ]
];
