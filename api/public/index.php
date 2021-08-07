<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (RequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write('{TEST OK}');
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
