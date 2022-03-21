<?php

namespace App\Models;

use Src\Bases\Model;

class Currency extends Model
{
    /**
     * Currency code of the model.
     *
     * @var string
     */
    protected $code;

    /**
     * JSON file where to save data.
     *
     * @var string
     */
    protected $jsonDir = __DIR__ . '/../../public/storage/exchangeRates.json';

    /**
     * Reads JSON file and returning JSON array.
     *
     * @param string
     * @return array
     * @throws \Exception
     */
    public function getExchangeRatesTable($base='EUR'): array
    {
        $json = $this->JSONDataGet();
        if($json[0] != 200){ return $json; }
        if($base != 'EUR'){
            $json = $this->JSONDataFilter($json[1],'code', $base);
            if($json[0] != 200){ return $json; }
            else{
                foreach ($json[1] as $object){
                    try{
                        $newBase = floatval($json[1][0]['rate']);
                        $rate = floatval($object['rate']);
                    }catch (\Exception $e){ return [500, "Rate is not a float type!"]; }
                    $json[1]['rate'] = $this->convertExchangeRatesBase($newBase, $rate);
                }
            }
        }
        return [200, $json];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @param float $newBase
     * @param float $value
     * @return float
     */
    protected function convertExchangeRatesBase(float $newBase, float $rate): float
    {
        $newRate = $newBase * $rate;
        return $newRate;
    }
}