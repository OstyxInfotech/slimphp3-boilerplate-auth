<?php

use App\Controllers\AuthController;

$cors_config = require __DIR__ . '/../config/cors.php';

$app->group('/api', function () use ($container) {
    $this->group('', function () {
        $this->post('/', function ($request, $response, $args) {
            return $response->withJson($request->getBody()->getContents());
        });

    })->add(new \App\Middleware\ApiMiddleware($container));

    $this->post('/login', \App\Controllers\ApiAuthController::class . ':postLogin');

//    $this->post('/verify-token', \App\Controllers\ApiAuthController::class.':postVerify');
})->add(new \Tuupola\Middleware\CorsMiddleware($cors_config));