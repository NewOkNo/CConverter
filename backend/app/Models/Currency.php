<?php

namespace App\Models;

use Src\Bases\Model;

class Currency extends Model
{
    /**
     * Models JSON location.
     *
     * @var string
     */
    protected $jsonLocation = __DIR__ . '/../../public/storage/exchangeRates/';

    /**
     * round() limit.
     *
     * @var int
     */
    protected $roundLimit = 6;

    /**
     * Models body path.
     *
     * @var array
     */
    //protected $body = ['rates'];

    /**
     * Models base structure.
     *
     * @var array
     */
    /*protected $baseStructure = [
        '$date' => [
            'base' => '$base',
            'rates' => '$rates'
        ]
    ];*/

    // TODO: fuck it (~5:00?)
    /**
     * Baltics panks last Exchange Rate update.
     *
     * @var int
     */
    //protected $ = ['rates'];

    /**
     * Date format.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * Models constructor.
     *
     * @param string|null $date
     */
    function __construct(?string $date)
    {
        $response = $this->getWorkingDate();
        //$this->jsonLocation .= $this->getDate($date).'.json';
    }

    /**
     * Gets Exchange Rates Table with wanted $base.
     *
     * @param string
     * @return array
     * @throws \Exception
     */
    public function getExchangeRatesTable(string $base='EUR'): array
    {
        $json = $this->JSONDataGet();
        if($json[0] != 200){
            if($json[0] == 404) $this->create();
        }
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

    /**
     * Returning checked received $date or today's date.
     *
     * @param string|null $date
     * @return array
     */
    protected function getWorkingDate(?string $date = null): array
    {
        $dateNow = \DateTime::createFromFormat($this->dateFormat, 'now');
        if($date){
            $date = \DateTime::createFromFormat($this->dateFormat, $date);
            if(!$date) return [400, "Incorrect date format! Must be ".$this->dateFormat];
            else if($date > $dateNow) return [400, "You can't use future dates!"];
            else return [200, $date->format($this->dateFormat)];
        } else return [200, $dateNow->format($this->dateFormat)];
    }

}