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
        $curency = Currency::class();
        $response->getBody()->write("bruh! t".$args['to']." f".$args['from']);
        return $response;
    }
}