<?php

namespace App\Controllers;

use App\Models\ExchangeRates;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Src\Bases\Controller;

class CurrencyController extends Controller{
    /**
     * GET request.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return  Response
     */
    public function get(Request $request, Response $response, array $args): Response
    {
        $date = (array_key_exists('date', $args)) ? $args['date'] : null;
        $base = (array_key_exists('base', $args)) ? $args['base'] : null;
        $currency = new ExchangeRates($date);
        //$resp = $currency->getExchangeRatesTable("CAD");
        $resp = $currency->getExchangeRatesTable($base);
        //$response->withStatus(400);
        //$response->getBody()->write("bruh! t".$args['to']." f".$args['from']."\n ");
        $response->getBody()->write(json_encode($resp[1]));
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}