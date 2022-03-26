<?php
//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// TODO: turn it on when finished
error_reporting(0);
ini_set('display_errors', 0);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../routes/cconverter.php';

?>