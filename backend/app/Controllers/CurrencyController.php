<?php

namespace App\Controllers;

use App\Models\Currency;
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
        $currency = new Currency();
        $resp = $currency->getExchangeRatesTable("CAD");
        //$response->withStatus(400);
        //$response->getBody()->write("bruh! t".$args['to']." f".$args['from']."\n ");
        $response->getBody()->write(json_encode($resp[1]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}