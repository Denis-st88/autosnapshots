<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;

use DomainException;
use App\Http\JsonResponse;
use App\Http\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Auth\Command\SignUpByEmail\Request\Command;
use App\Auth\Command\SignUpByEmail\Request\Handler;

class RequestAction implements RequestHandlerInterface
{
    private Handler $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @psalm-var array{email:?string, password:?string} $data
         */
        $data = json_decode((string)$request->getBody(), true);

        $command = new Command();
        $command->email = trim($data['email'] ?? '');
        $command->password = trim($data['password'] ?? '');

        try {
            $this->handler->handle($command);
            return new EmptyResponse(201);
        } catch (DomainException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }
}
