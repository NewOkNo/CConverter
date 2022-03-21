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
    public function getExchangeRateTable($base='EUR'){
        $json = $this->JSONDataGet();
        if($json[0] != 200){ return $json; }
        if($base != 'EUR'){
            $json = $this->get('code', $base);
            if($json[0] != 200){ return $json; }
            else{
                foreach ($json[1] as $cur){
                    $json = $this->convertExchangeRateBase($cur['code']);
                    if($json[0] != 200){ return $json; }
                }
            }
        }
        return [200, $json];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @param string
     * @return array
     * @throws \Exception
     */
    public function convertExchangeRateBase($base='EUR'){
        $json = $this->JSONDataGet();
        if($json[0] != 200){ return $json; }
        if($base != 'EUR'){
            if($json[1][$base]!=null){
                $json = $this->convertExchangeRateBase($base);
                if($json[0] != 200){ return $json; }
            } else return [400, "Wrong base code"];
        }
        return [200, $json];
    }
}