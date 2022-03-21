<?php

namespace App\Models;

use Src\Bases\Model;

class Currency extends Model
{
    /**
     * JSON file where to save data.
     *
     * @var string
     */
    protected $jsonDir = __DIR__ . '/../../public/storage/exchangeRates.json';

    /**
     * round() limit.
     *
     * @var int
     */
    protected $roundLimit = 6;


    // TODO: think about this shit
    /**
     * Model path.
     *
     * @var array
     */
    protected $modelPath = ['rates'];

    /**
     * Gets Exchange Rates Table with wanted $base.
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
            //$json = $this->JSONDataFilter($json[1],'code', $base);
            if(!$json[1]->rates->$base){ return $json; }
            else{
                //$json[1]->base = $base;
                $baseRate = $json[1]->rates->$base;
                unset($json[1]->rates->$base);
                foreach ($json[1]->rates as $code => $rate){
                    /*try{
                        $rate = floatval($rate);
                    }catch (\Exception $e){ return [500, "Rate is not a float type!"]; }*/
                    $json[1]->rates->$code = $this->convertExchangeRatesBase(floatval($baseRate), floatval($rate));
                }
                $json[1]->rates->EUR = round(1.0 / $baseRate, $this->roundLimit);
            }
        }
        return [200, $json[1]];
    }

    /**
     * Calculating new Exchange Rate.
     *
     * @param float $baseRate
     * @param float $rate
     * @return float
     */
    protected function convertExchangeRatesBase(float $baseRate, float $rate): float
    {
        $newRate = $baseRate * $rate;
        return round($newRate, $this->roundLimit);
    }

}