<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
//$app->addErrorMiddleware(false, false, false);

// Add routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

//$app->get('/cconverter[?to={to}][&from={from}]]', [\App\Controllers\CurrencyController::class, 'get']);
$app->get('/cconverter[/{to:[A-Za-z]{3}}[/{from:[A-Za-z]{3}}]]', [\App\Controllers\CurrencyController::class, 'get']);

$app->run();