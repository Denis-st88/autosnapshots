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
    protected static function json(string $method, string $path, array $body = []): ServerRequestInterface
    {
        $request = self::request($method, $path)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($body, JSON_THROW_ON_ERROR));
        return $request;
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
