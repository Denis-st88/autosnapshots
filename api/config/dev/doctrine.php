<?php

declare(strict_types=1);

use App\Data\Doctrine\FixDefaultSchemaSubscriber;

return [
    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => null,
            /**
             * Processes start php-fpm how www-data.
             * Processes start php-cli how root.
             * To avoid an error  accessing the cache "not enough rights"
             */
            'proxy_dir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
            'subscribers' => [
                FixDefaultSchemaSubscriber::class
            ]
        ]
    ]
];
