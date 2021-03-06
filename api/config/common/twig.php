<?php

declare(strict_types=1);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Psr\Container\ContainerInterface;
use Twig\Extension\ExtensionInterface;
use App\Frontend\FrontendUrlTwigExtension;

return [
    Environment::class => function (ContainerInterface $container): Environment {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *      debug:bool,
         *      template_dirs:array<string,string>,
         *      cache_dir:string,
         *      extensions:string[],
         * } $config
         */
        $config = $container->get('config')['twig'];

        $loader = new FilesystemLoader();

        foreach ($config['template_dirs'] as $alias => $dir) {
            $loader->addPath($dir, $alias);
        }

        $environment = new Environment($loader, [
            'cache' => $config['debug'] ? false : $config['cache_dir'],
            'debug' => $config['debug'],
            'strict_variables' => $config['debug'],
            'auto_reload' => $config['debug'],
        ]);

        if ($config['debug']) {
            $environment->addExtension(new DebugExtension());
        }

        foreach ($config['extensions'] as $class) {
            /** @var ExtensionInterface $extension */
            $extension = $container->get($class);
            $environment->addExtension($extension);
        }

        return $environment;
    },

    'config' => [
        'twig' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'template_dirs' => [
                FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../../templates'
            ],
            'cache_dir' => __DIR__ . '/../../var/cache/twig',
            'extensions' => [
                FrontendUrlTwigExtension::class
            ]
        ]
    ]
];
