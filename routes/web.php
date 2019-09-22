<?php

use App\Controllers\AuthController;

$app->group('', function () use ($container) {
    $this->group('', function () {
        $this->get('/', \App\Controllers\HomeController::class . ':index')->setName('home');
        $this->get('/logout', AuthController::class . ':getLogout')->setName('auth.logout');

        $this->get('/change-password', \App\Controllers\PasswordsController::class . ':getChangePassword')->setName('auth.password.change');
        $this->post('/change-password', \App\Controllers\PasswordsController::class . ':postChangePassword');
    })->add(new \App\Middleware\AuthMiddleware($container));

    $this->group('', function () {
        $this->get('/register', AuthController::class . ':getRegister')->setName('auth.register');
        $this->post('/register', AuthController::class . ':postRegister');

        $this->get('/login', AuthController::class . ':getLogin')->setName("auth.login");
        $this->post('/login', AuthController::class . ':postLogin');
    })->add(new \App\Middleware\GuestMiddleware($container));
})->add(new \App\Middleware\CsrfViewMiddleware($container))->add($container->csrf);;
