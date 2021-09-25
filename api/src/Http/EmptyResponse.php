<?php

declare(strict_types=1);

namespace App\Http;

use Slim\Psr7\Response;
use Slim\Psr7\Factory\StreamFactory;

class EmptyResponse extends Response
{
    public function __construct(int $status = 204)
    {
        parent::__construct(
            $status,
            null,
            (new StreamFactory())->createStreamFromResource(fopen('php://temp', 'rb'))
        );
    }
}
