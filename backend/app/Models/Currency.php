<?php

namespace App\Models;

use Src\Bases\Model;

class Currency extends Model
{
    /**
     * Model's JSON location.
     *
     * @var string
     */
    //protected $jsonLocation = __DIR__ . '/../../public/storage/exchangeRates/';

    /**
     * Path to the model's JSON cache location.
     *
     * @var string
     */
    protected $jsonLocation = __DIR__ . '/../../public/storage/exchangeRates';

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

    // TODO: fuck it (~18:00? for the Leedu Pank && ~00:00? for the Eesti Pank)
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
     * Date of the working model.
     *
     * @var string
     */
    protected $date;

    /**
     * Errors array.
     *
     * @var array
     */
    protected $errors = [];

    //'pattern' => 'gesmes\:\Envelope:[Cube:[Cube:i[imported:$_date]]{1}+Cube:i[currency:$_code+rate:$_rate]{?}]{1}'
    /**
     * Request Sources.
     *
     * @var array
     */
    protected $requestSources = [
        'Eesti Pank' => [
            'xml' => [
                'link' => 'https://haldus.eestipank.ee/et/export/currency_rates?_date=$_date&type=xml',
                'pattern' => [
                    'base' => 'Cube:Cube',
                    'date' => '_base["imported"]',
                    'rates' => '$_base[1+]',
                    'code' => '$_rates:currency',
                    'rate' => '$_rates:rate'
                ],
                'saveTo' => './storage/temp/EPER'
            ],
            'json' => [
                'link' => 'https://www.eestipank.ee/api/get',
                'method' => 'POST',
                'body' => 'url: "en/rest/currency_rates"',
                'pattern' => 'imported:$_date|rates:$_rates'
            ]
        ],
    ];

    /**
     * Models constructor.
     *
     * @param string|null $date
     */
    function __construct(?string $date = null)
    {
        $response = $this->getDate($date);
        if($response[0] != 200) {
            $this->errors[] = $response;
            $response = $this->getDate(null);
        }
        $this->date = $response[1];
        $ymd = preg_split('/-/', $this->date);
        $this->jsonLocation .= '/'.$ymd[0].'/'.$ymd[1];
        $this->jsonName = $ymd[2];
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
        $response = $this->JSONDataGet();
        if($response[0] != 200){
            if($response[0] == 404){
                $response = $this->requestData();
                if($response[0] != 200) return $response;
            } else return $response;
        }
        if($base != 'EUR'){
            //$json = $this->JSONDataFilter($json[1],'code', $base);
            if(!$response[1]->rates->$base){ return $response; }
            else{
                //$json[1]->base = $base;
                $baseRate = $response[1]->rates->$base;
                unset($response[1]->rates->$base);
                foreach ($response[1]->rates as $code => $rate){
                    /*try{
                        $rate = floatval($rate);
                    }catch (\Exception $e){ return [500, "Rate is not a float type!"]; }*/
                    $response[1]->rates->$code = $this->convertExchangeRatesBase(floatval($baseRate), floatval($rate));
                }
                $response[1]->rates->EUR = round(1.0 / $baseRate, $this->roundLimit);
            }
        }
        return [200, $response[1]];
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
    protected function getDate(?string $date): array
    {
        $dateNow = \DateTime::createFromFormat($this->dateFormat, date('Y-m-d'));
        if($date){
            $date = \DateTime::createFromFormat($this->dateFormat, $date);
            if(!$date) return [400, "Incorrect date format! Must be ".$this->dateFormat];
            else if($date > $dateNow) return [400, "You can't use future dates!"];
            else return [200, $date->format($this->dateFormat)];
        } else return [200, $dateNow->format($this->dateFormat)];
    }

    /**
     * Requesting data from different $requestSources.
     *
     * @return array
     */
    protected function requestData(): array
    {
        return [200];
    }

    /**
     * Requesting data from different $requestSources.
     *
     * @return array
     */
    public function getDataWithPattern(): array
    {
        $data = $this->requestSources['Eesti Pank']['xml'];
        $data['saveTo'] .= $this->date . ".xml";

        $link = str_replace('$_date', $this->date, $data['link']);

        /*$html = file_get_contents($link);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $_base = simplexml_import_dom($doc);*/

        if (file_put_contents($data['saveTo'], file_get_contents($link))){

            $_base = simplexml_load_file($data['saveTo']) or die("Error: Cannot create object");

            $pattern = $data['pattern'];
            /*$_base = $pattern['base'];
            $_date = $pattern['date'];
            $_rates = $pattern['rates'];
            $_code = $pattern['code'];
            $_rate = $pattern['rate'];*/

            if($pattern['base']){
                $path = preg_split('/:/', $pattern['base']);
                foreach($path as $p){
                    $_base = $_base->$p;
                }
            }

            //$path = preg_split(':', $pattern['date']);
            //$date = ${$pattern['date']};

            return [200, $_base['imported']];
        }
        return [200, 'not nice'];
    }

}