<?php

declare(strict_types=1);

namespace Test\Functional;

use Slim\App;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

class WebTestCase extends TestCase
{
    /**
     * @param string $method
     * @param string $path
     * @return ServerRequestInterface
     */
    protected static function json(string $method, string $path): ServerRequestInterface
    {
        return self::request($method, $path)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $method
     * @param string $path
     * @return ServerRequestInterface
     */
    protected static function request(string $method, string $path): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }

    /**
     * @return App
     */
    protected function app(): App
    {
        /** @var App $app */
        return (require __DIR__ . '/../../config/app.php')($this->container());
    }

    /**
     * @return ContainerInterface
     */
    private function container(): ContainerInterface
    {
        /** @var ContainerInterface $container */
        return require __DIR__ . '/../../config/container.php';
    }
}
