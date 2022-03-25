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
     * Model's object structure.
     *
     * @var array
     */
    /*protected $objectStructure = [
        'base' => [
            'base' => '_base',
            'rates' => '_rates'
        ],
        'rates' => [
            '_code' => '_rate'
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
    //'pattern' => 'imported:$_date|rates:$_rates'
    /**
     * Request Sources.
     *
     * @var array
     */
    protected $requestSources = [
        'Eesti Pank' => [
            'json' => [
                'type' => 'json',
                'link' => 'https://www.eestipank.ee/api/get',
                'method' => 'POST',
                'body' => ['url' => 'en/rest/currency_rates'],
                'pattern' => [
                    'base' => [
                        '_base' => '',
                        '_date' => '_base->imported',
                        '_rates' => '_base->rates'
                    ],
                    'rates' => [
                        '_code' => 'code',
                        '_rate' => 'rate'
                    ]
                ]
            ],
            'xml' => [
                'type' => 'xml',
                'link' => 'https://haldus.eestipank.ee/et/export/currency_rates?date=$_date&type=xml',
                'pattern' => [
                    'base' => [
                        '_base' => 'Cube->Cube',
                        '_date' => '_base:imported',
                        '_rates' => '_base->Cube'
                    ],
                    'rates' => [
                        '_code' => ':currency',
                        '_rate' => ':rate'
                    ]
                ],
                'saveTo' => './storage/temp/EPER-'
            ]
        ],
        'Leedu Pank' => [
            'xml' => [
                'type' => 'xml',
                'link' => 'https://www.lb.lt/en/currency/daylyexport/?xml=1&class=Eu&type=day&date_day=$_date',
                'pattern' => [
                    'base' => [
                        '_base' => '',
                        '_date' => '_base->data:1',
                        '_rates' => ''
                    ],
                    'rates' => [
                        '_code' => 'valiutos_kodas',
                        '_rate' => 'santykis'
                    ]
                ],
                'saveTo' => './storage/temp/LPER-'
            ],
            'html' => [
                'type' => 'LPhtml',
                'link' => 'https://www.lb.lt/fxrates_csv.lb?tp=EU&rs=1&dte=$_date',
                'pattern' => [
                    '_code' => ':1',
                    '_rate' => ':2',
                    '_date' => ':3'
                ]
            ]
        ],
        /*'exchangeratesapi' => [
            'json' => [
                'type' => 'json',
                'link' => 'https://api.exchangeratesapi.io/v1/$_date?access_key=$_key',
                'key' => (string)$_ENV['EXCHANGE_RATES_API_KEY'],
                'method' => 'GET',
                'body' => [],
                'pattern' => [
                    'base' => [
                        '_base' => '',
                        '_date' => '_base->date',
                        '_rates' => '_base->rates'
                    ],
                    'rates' => [
                        '_code' => 'code',
                        '_rate' => 'rate'
                    ]
                ]
            ]
        ]*/
    ];

    /**
     * Acceptable http status codes.
     *
     * @var string
     */
    protected $acStatusCodes = [100, 299];

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
        foreach ($this->requestSources as $source){
            foreach ($source as $method){
                $response = $this->getData($method);
                if($response[0] == 200) return [200, $response[1]];
            }
        }
        return [500, "All requests were failed"];
    }

    /**
     * Requesting data from different $requestSources.
     *
     * @return array
     */
    public function getData(array $data): array
    {
        //$data = $this->requestSources['Eesti Pank']['xml'];
        //$data = $this->requestSources['Eesti Pank']['json'];
        $type = $data['type'];
        $link = str_replace('$_date', $this->date, $data['link']);

        if($type == 'xml'){
            $data['saveTo'] .= $this->date . ".xml";

            /*$html = file_get_contents($link);
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $_base = simplexml_import_dom($doc);*/

            $response = file_get_contents($link);
            preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $http_response_header[0], $out);
            $respCode = $out[1];
            if($respCode <= $this->acStatusCodes[0] || $respCode >= $this->acStatusCodes[1]) return [500, 'Request fail'];

            if (file_put_contents($data['saveTo'], $response)){
                $start = simplexml_load_file($data['saveTo']) or die("Error: Cannot create object");
            }
            else return [500, 'File saving fail'];
        }
        else if($type == 'json'){
            $postdata = http_build_query(
                array(
                    key($data['body']) => reset($data['body'])
                )
            );
            $opts = array('http' =>
                array(
                    'method' => $data['method'],
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            $response = file_get_contents($link, context: $context);
            preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $http_response_header[0], $out);
            $respCode = $out[1];
            if($respCode <= $this->acStatusCodes[0] || $respCode >= $this->acStatusCodes[1]) return [500, 'Request fail'];
            $start = json_decode($response);
            //return [200, $start->imported];
        }
        else if($type == 'LPhtml'){
            $response = file_get_contents($link);
            preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $http_response_header[0], $out);
            $respCode = $out[1];
            if($respCode <= $this->acStatusCodes[0] || $respCode >= $this->acStatusCodes[1]) return [500, 'Request fail'];

            $lines = preg_split("/\r\n|\r|\n/", $response);
            $obj = [];
            $obj['_base'] = 'EUR';
            $_date = preg_split('/,/',$lines[0])[3];
            if($_date != $this->date) return [500, "Incorrect date returned"];
            $obj['_date'] = $_date;
            $_rates = [];
            foreach($lines as $line){
                $_code = preg_split('/,/',$line)[1];
                $_rate = preg_split('/,/',$line)[2];
                $_rates[] = ['_code' => $_code, '_rate' => $_rate];
            }
            $obj['_rates'] = $_rates;

            $response = $this->createObject($obj);
            if($response[0]!=200) return $response;

            return [200, $response[1]];
        }else { return [404, 'Unknown type']; }


        $base = $data['pattern']['base'];
        $response = $this->getDataViaPattern($start, $base);
        if($response[0]!=200) return $response;
        if($response[1]['_date'] != $this->date) return [500, "Incorrect date returned"];
        $response[1]['_base'] = 'EUR';
        $obj = $response[1];

        $rates = $data['pattern']['rates'];
        $_rates = [];
        foreach ($response[1]['_rates'] as $rate){
            if($type=='json') { $rate = end($rate); }
            $response = $this->getDataViaPattern($rate, $rates);
            if($response[0]!=200) return $response;
            $response[1]['_rate'] = str_replace(',', '.', (string)$response[1]['_rate']);
            if(!floatval((string)$response[1]['_rate'])) continue;
            $_rates[] = $response[1];
        }
        $obj['_rates'] = $_rates;
        //return [200, $response[1]];
        $response = $this->createObject($obj);
        if($response[0]!=200) return $response;

        return [200, $response[1]];
    }

    /**
     * Custom method to get data via pattern.
     *
     * @param object $startingPoint
     * @param array $pattern
     * @return array
     */
    protected function getDataViaPattern(object $startingPoint, array $pattern): array
    {
        foreach ($pattern as $patkey => $patvalue){
            $path = [];
            $items = preg_split('/->/', $patvalue);
            foreach($items as $item){
                $item = preg_split('/:/', $item);
                while(array_key_exists($item[0], $path)){ $item[0] = $item[0].'|'; }
                if(count($item)>1){
                    $path[$item[0]] = $item[1];
                } else{ $path[$item[0]] = null; }
            }
            if($patkey == key($pattern) || !array_key_exists(key($path), $pattern)){ ${$patkey} = $startingPoint;}
            else if(!is_string($pattern[key($path)])) { ${$patkey} = ${key($path)}; }
            else return [400, "Wrong pattern elements positioning"];
            foreach ($path as $pathkey => $pathitem){
                if($pathkey){while (str_ends_with($pathkey, '|')) { $pathkey = substr($pathkey, 0, -1); }}
                if($pathkey && !in_array($pathkey, array_keys($pattern))){ ${$patkey} = ${$patkey}->$pathkey; }
                if($pathitem){ ${$patkey} = ${$patkey}[$pathitem]; }
                //if(ctype_digit($pathitem)) {$pathitem}
            }
            $pattern[$patkey] = ${$patkey};
        }
        return [200, $pattern];
    }

    /**
     * Creates model's object.
     *
     * @param array $object
     * @return array
     */
    public function createObject(array $object): array{
        //$rates = $this->objectStructure['rates'];
        $rates = [];
        foreach ($object['_rates'] as $rate){
            /*$_rate = str_replace('_code', $rate['_code'], $rates);
            $_rate = str_replace('_rate', $rate['_rate'], $_rate);*/
            //return [200, gettype($rate['_code'])];
            $rates[(string)$rate['_code']] = (float)$rate['_rate'];
        }

        /*$base = $this->objectStructure['base'];
        $_base = str_replace('_base', $object['_code'], $base);
        $_base = str_replace('_rates', $_rates, $_base);*/
        $base['base'] = (string)$object['_base'];
        $base['rates'] = $rates;

        $obj[$this->date] = $base;

        return [200, $obj];
    }
}