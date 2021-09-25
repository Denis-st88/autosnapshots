<?php

declare(strict_types=1);

use Slim\App;
use App\Http\Action\HomeAction;
use App\Http\Action\V1\Auth\SignUp\RequestAction;


return static function (App $app): void {
    $app->get('/', HomeAction::class);
    $app->post('/v1/auth/sign-up', RequestAction::class);
};
