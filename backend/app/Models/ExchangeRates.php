<?php

namespace App\Models;

use Src\Bases\Model;

class ExchangeRates extends Model
{

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
                'body' => ['url' => 'en/rest/currency_rates?imported=$_date'],
                'pattern' => [

                    '_date' => 'imported',
                    '_rates' => 'rates->[]',
                    '_code' => '_rates->0->code',
                    '_rate' => '_rates->0->rate',
                ]
            ],
            'xml' => [
                'type' => 'xml',
                'link' => 'https://haldus.eestipank.ee/et/export/currency_rates?imported=$_date&type=xml',
                'pattern' => [

                    '_stpoint' => 'Cube->Cube',
                    '_date' => '_stpoint->@attributes->imported',
                    '_rates' => '_stpoint->Cube->[]',
                    '_code' => '_rates->@attributes->currency',
                    '_rate' => '_rates->@attributes->rate',
                ],
                'saveTo' => './storage/temp/EPER-'
            ]
        ],
        'Leedu Pank' => [
            'xml' => [
                'type' => 'xml',
                'link' => 'https://www.lb.lt/en/currency/daylyexport/?xml=1&class=Eu&type=day&date_day=$_date',
                'saveTo' => './storage/temp/LPER-',
                'dateFormat' => 'd-m-Y',
                'pattern' => [
                    '_date' => 'item->1->data',
                    '_rates' => 'item->[]',
                    '_code' => '_rates->valiutos_kodas',
                    '_rate' => '_rates->santykis'
                ],
            ],
            'html' => [
                'type' => 'html',
                'link' => 'https://www.lb.lt/fxrates_csv.lb?tp=EU&rs=1&dte=$_date',
                'pattern' => [
                    '_date' => '0->3',
                    '_rates' => '[]',
                    '_code' => '_rates->1',
                    '_rate' => '_rates->2'
                ]
            ]
        ],
        'exchangeratesapi' => [
            'json' => [
                'type' => 'json',
                'link' => 'http://api.exchangeratesapi.io/v1/$_date?access_key=$_key',
                'key' => 'EXCHANGE_RATES_API_KEY',
                'method' => 'GET',
                'body' => [],
                'pattern' => [
                    '_date' => 'date',
                    '_rates' => 'rates->[]',
                    '_code' => '_rates->@selfkey',
                    '_rate' => '_rates->@selfvalue'
                ]
            ]
        ]
    ];

    /**
     * Models constructor.
     *
     * @param string|null $date
     */
    function __construct(?string $date = null)
    {
        $this->clearTemp();
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
     * @param string|null $base
     * @return array
     */
    public function getExchangeRatesTable(?string $base='EUR'): array
    {
        if(!$base) $base = 'EUR';
        $response = $this->JSONDataGet();
        if($response[0] != 200){
            if($response[0] == 404){
                $response = $this->requestData();
                if($response[0] != 200) return $response;
                $response2 = $this->JSONDataPut($response[1]);
                if($response2[0] != 200) {
                    // TODO: ignore all errors like that one and log them
                    error_log("data creation error");
                }
            } else return $response;
        }
        if($base != 'EUR'){
            //$json = $this->JSONDataFilter($json[1],'code', $base);
            if(!$response[1]->rates->$base){ return $response; }
            else{
                //$json[1]->base = $base;
                $response[1]->base = $base;
                $baseRate = $response[1]->rates->$base;
                unset($response[1]->rates->$base);
                foreach ($response[1]->rates as $code => $rate){
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
     * @param string $format
     * @return array
     */
    protected function getDate(?string $date, string $format = 'Y-m-d'): array
    {
        $dateNow = \DateTime::createFromFormat($format, date($format));
        if($date){
            $date = \DateTime::createFromFormat($format, $date);
            if(!$date) return [400, "Error while creating a date!"];
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
        //return $response;
        return [500, "All requests were failed"];
    }

    /**
     * Requesting data from different $requestSources.
     *
     * @param array $data
     * @return array
     */
    protected function getData(array $data): array
    {
        $link = str_replace('$_date', $this->date, $data['link']);
        $response = $this->{'getData'.$data['type']}($data, $link);
        if($response[0]!=200) return $response;
        $start = $response[1];
        //$type = $data['type'];
        //$link = str_replace('$_date', $this->date, $data['link']);

        /*if($type == 'xml'){

        }*/
        /*else if($type == 'json'){

        }*/
        /*else if($type == 'html'){

        }else { return [404, 'Unknown type']; }*/

        $response = $this->getDataViaPattern($start, $data['pattern']);
        if($response[0]!=200) return $response;
        unset($response[1]['_date']['/modifier/']);
        unset($response[1]['_rates']['/modifier/']);
        unset($response[1]['_rate']['/modifier/']);
        unset($response[1]['_code']['/modifier/']);
        if(!$response[1]['_date'] || empty($response[1]['_date']) ||
            !$response[1]['_rates'] || empty($response[1]['_rates']) ||
            !$response[1]['_rate'] || empty($response[1]['_rate']) ||
            !$response[1]['_code'] || empty($response[1]['_code'])) return [500, "Data wasn't returned!"];

        if(is_array($response[1]['_date'])) $response[1]['_date'] = $response[1]['_date'][0];
        $dateFormat = $data['dateFormat'];
        if($dateFormat && $dateFormat!=$this->dateFormat){
            $response2 = $this->getDate($response[1]['_date'], $dateFormat);
            if($response2[0] != 200) return $response2;
            $response[1]['_date'] = $response2[1];
        }
        if($response[1]['_date'] != $this->date) return [500, "Incorrect date returned"];
        $response[1]['_base'] = 'EUR';

        $rates = [];
        foreach ($response[1]['_code'] as $codeKey => $codeValue){
            if(is_array($codeValue)) $codeValue = $codeValue[0];
            if(!preg_match("/^[A-Z]{3}$/", $codeValue)) continue;
            $rates[$codeKey] = [];
            $rates[$codeKey]['_code'] = $codeValue;
        }
        foreach ($response[1]['_rate'] as $rateKey => $rateValue){
            if(is_array($rateValue)) $rateValue = $rateValue[0];
            $rateValue = str_replace(',', '.', $rateValue);
            if(!is_numeric($rateValue)) continue;
            if(!$rates[$rateKey]) continue;
            $rates[$rateKey]['_rate'] = $rateValue;
        }

        /*$_rates = [];

        foreach ($rates as $rate){
            if(!$rate) continue;
            $rates[$rate['_code']] = $rate['_rate'];
        }*/

        $response[1]['_rates'] = $rates;
        unset($response[1]['_code']);
        unset($response[1]['_rate']);

        $response = $this->createObject($response[1]);
        if($response[0]!=200) return $response;

        return [200, $response[1]];
    }

    /**
     * Requesting data from JSON.
     *
     * @param array $data
     * @return array
     */
    protected function getDatajson(array $data, string $link): array{
        if(stristr($data['link'], '$_key')) $data['link'] = str_replace('$_key', $_ENV[$data['key']], $data['link']);
        if(stristr($data['link'], '$_date')) $link = str_replace('$_date', $this->date, $data['link']);
        //return [100, $link];
        if($data['method']=='POST'){
            $data['body'] = str_replace('$_date', $this->date, $data['body']);
            $postdata = http_build_query([key($data['body']) => reset($data['body'])]);
            $opts = ['http' => [
                'method' => $data['method'],
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            ]
            ];
            $context = stream_context_create($opts);
            $response = $this->makeRequest($link, $context);
        }
        else{
            $response = $this->makeRequest($link);
        }

        if($response[0]!=200) return $response;

        $start = json_decode($response[1]);
        return [200, $start];
    }

    /**
     * Requesting data from HTML.
     *
     * @param array $data
     * @return array
     */
    protected function getDatahtml(array $data, string $link): array{
        $response = $this->makeRequest($link);
        if($response[0]!=200) return $response;

        $start = [];
        $lines = preg_split("/\r\n|\r|\n/", $response[1]);
        foreach ($lines as $idx => $line) $start[$idx] = preg_split('/,/',$line);

        return [200, $start];
    }

    /**
     * Requesting data from HTML.
     *
     * @param array $data
     * @return array
     */
    protected function getDataxml(array $data, string $link): array{
        $data['saveTo'] .= $this->date . ".xml";

        $response = $this->makeRequest($link);
        if($response[0]!=200) return $response;

        if(!file_exists($this->tempPath)) mkdir($this->tempPath, 0777, true);
        if (file_put_contents($data['saveTo'], $response[1])){
            $start = simplexml_load_file($data['saveTo'], null, LIBXML_NOCDATA) or die("Error: Cannot create object");
        }
        else return [500, 'Fail while saving a file'];
        return [200, $start];
    }

    /**
     * Custom method to get data via pattern.
     *
     * @param object|array $startingPoint
     * @param array $pattern
     * @return array
     */
    protected function getDataViaPattern(object|array $startingPoint, array $pattern): array
    {
        foreach ($pattern as $patkey => $patvalue){
            /*$path = [];
            $items = preg_split('/->/', $patvalue);
            foreach($items as $item){
                $item = preg_split('/:/', $item);
                while(array_key_exists($item[0], $path)){ $item[0] = $item[0].'|'; }
                if(count($item)>1){
                    $path[$item[0]] = $item[1];
                } else{ $path[$item[0]] = null; }
            }*/
            $path = preg_split('/->/', $patvalue);
            $firstEllement = $path[0];
            if($patkey == key($pattern) || !array_key_exists($firstEllement, $pattern)){ ${$patkey} = (array) $startingPoint;}
            else if(!is_string($pattern[$firstEllement])) { ${$patkey} = (array) ${$firstEllement}; }
            else return [400, "Wrong pattern elements positioning"];
            $itemsKeysArray = [null];
            $itemsOutputArray = [];
            foreach ($path as $pathkey){
                if(${$patkey}['/modifier/']){
                    if(${$patkey}['/modifier/'] == '[]'){
                        $arr = (array)${$patkey};
                        unset($arr['/modifier/']);
                        $itemsKeysArray = array_keys($arr);
                    }
                }
                foreach($itemsKeysArray as $itemKey){
                    //if($pathkey){while (str_ends_with($pathkey, '|')) { $pathkey = substr($pathkey, 0, -1); }}
                    if($pathkey=='[]') ${$patkey}['/modifier/'] = "[]";
                    /*else if($pathkey=='@selfkey') $itemsOutputArray[$itemKey] = key(((array)${$patkey}));
                    else if($pathkey=='@selfvalue') $itemsOutputArray[$itemKey] = ((array)${$patkey})[$pathkey];*/
                    else if(($pathkey || $pathkey == '0') && !in_array($pathkey, array_keys($pattern))) {
                        if($itemKey || $itemKey == '0'){
                            if($pathkey=='@selfkey') $itemsOutputArray[$itemKey] = $itemKey;
                            else if($pathkey=='@selfvalue') $itemsOutputArray[$itemKey] = ((array)${$patkey})[$itemKey];
                            else if(!$itemsOutputArray[$itemKey]) $itemsOutputArray[$itemKey] = (array)((array)((array)${$patkey})[$itemKey])[$pathkey];
                            else $itemsOutputArray[$itemKey] = $itemsOutputArray[$itemKey][$pathkey];
                        }
                        else ${$patkey} = (array)((array)${$patkey})[$pathkey];
                    }
                    /*if($pathitem){
                        if($pathitem=='[]') ${$patkey}['/modifier/'] = "[]";
                        else ${$patkey} = ${$patkey}[$pathitem];
                    }*/
                }
                if(!empty($itemsOutputArray)) ${$patkey} = $itemsOutputArray;
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
        $rates = [];
        foreach ($object['_rates'] as $rate){
            $rates[(string)$rate['_code']] = (float)$rate['_rate'];
        }

        //$base['date'] = $this->date;
        $base['date'] = (string)$object['_date'];
        $base['base'] = (string)$object['_base'];
        $base['rates'] = (object)$rates;

        return [200, (object)$base];
    }
}