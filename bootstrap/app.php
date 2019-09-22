<?php

use Respect\Validation\Validator as v;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

(Dotenv\Dotenv::create(__DIR__.'/../'))->load();
date_default_timezone_set(getenv('APP_TIMEZONE'));
$app=new \Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('APP_ENV') == 'dev',
        'appName' => getenv('APP_NAME'),
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false,
    ],
]);

$container=$app->getContainer();

require __DIR__.'/database.php';

$container['validator']=function ($container){
    return new App\Validation\Validator();
};

$container['csrf'] = function ($container){
    return new \Slim\Csrf\Guard;
};

$container['auth'] = function ($container){
    return new App\Auth\Auth;
};

$container['flash']=function ($container){
    return new \Slim\Flash\Messages;
};


$container['view']=function ($container){
    $view=new \Slim\Views\Twig(__DIR__.'/../resources/views', [
        'cache' => getenv('APP_ENV') === 'dev' ? false : __DIR__.'/../storage/cache/views',
    ]);

    $router=$container->get('router');
    $uri=\Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    $view->getEnvironment()->addGlobal('app', [
        'name' => getenv('APP_NAME')
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};


$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));

$container['csrf'] = function ($c) {
    $csrf = new \Slim\Csrf\Guard();
    $csrf->setPersistentTokenMode(true);
    return $csrf;
};

v::with('App\\Validation\\Rules\\');

require_once __DIR__.'/../routes/web.php';
require_once __DIR__.'/../routes/api.php';

$app->run();