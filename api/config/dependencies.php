<?php

declare(strict_types=1);

$files = array_merge(
    glob(__DIR__ . '/common/*.php'),
    glob(__DIR__ . '/' . (getenv('APP_ENV') ?: 'prod')) ?: []
);

$configs = array_map(
    static function (string $file): array {
        /**
         * @var array
         * @noinspaction PhpIncludeInspection
         * @psalm-suppress UnresolvableInclude
         */
        return require $file;
    },
    $files
);

return array_merge_recursive(...$configs);
