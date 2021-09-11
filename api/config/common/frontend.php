<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use App\Frontend\FrontendUrlGenerator;

return [
    FrontendUrlGenerator::class => function (ContainerInterface $container): FrontendUrlGenerator {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{url:string} $config
         */
        $config = $container->get('config')['frontend'];

        return new FrontendUrlGenerator($config['url']);
    },

    'config' => [
        'frontend' => [
            'url' => getenv('FRONTEND_URL')
        ]
    ]
];
