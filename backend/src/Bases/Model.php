<?php

namespace Src\Bases;

abstract class Model{

    //// Base settings ////

    /**
     * Models JSON location.
     *
     * @var string
     */
    //protected $jsonLocation = __DIR__ . '/../../public/storage/'.__CLASS__.'.json';

    /**
     * Path to the model's JSON cache location.
     *
     * @var string
     */
    protected $jsonLocation = __DIR__ . '/../../public/storage';

    /**
     * JSON cache file name.
     *
     * @var string
     */
    protected $jsonName = __CLASS__;

    /**
     * Models body path.
     *
     * @var array
     */
    //protected $body = ['dody'];

    //// Protected Functions ////

    /**
     * Saves new object to the JSON file.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    protected function JSONDataPut(array $data): array
    {
        // TODO: test
        if(!file_exists($this->jsonLocation)) mkdir($this->jsonLocation);
        $file = fopen($this->jsonLocation.'/'.$this->jsonName.'.json', 'w');
        if(!$file) return [500, "Can't create a file!"];
        if(!fwrite($file, json_encode($data))) return [500, "Can't write in a file!"];
        fclose($file);
        return [200, true];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @return array
     */
    protected function JSONDataGet(): array
    {
        $rawjson = file_get_contents($this->jsonLocation.'/'.$this->jsonName.'.json');
        if(!$rawjson) return [404, "File is not found!"];
        $json = json_decode($rawjson);
        if(!$json) return [500, "Impossible to decode JSON!"];
        return [200, $json];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @param array $json
     * @param string|array $key
     * @return array|mixed|void
     */
    /*protected function JSONDataFilter(array $json, string|array $key): array
    {
        if(is_array($key)){
            $path = "json";
            foreach ($key as $_key) $path.'['.$_key.']';
            try{
                $value = ${$path};
            }catch (\Exception){ return [400, "Such key-path does not exist!"]; }
        } else{
            if(!$value = $json[1][$key])return [400, "Such key does not exist!"];
        }
        return [200, $value];
        /*$newjson = [];
        if(!$json[0][$key])return [400, "Key in not exists!"];
        foreach ($json as $object){
            if($object[$key] == $value) $newjson[] = $object;
        }
        if($newjson) return [200, $newjson];
        else return [300, "Objects with such key value not found!"];*/
    //}

    //// Public Functions ////

    /**
     * Get all the models from the database.
     *
     * @return array
     */
    public static function all()
    {
        return [];
    }

    /**
     * Get model by key value.
     *
     * @param string|array $key
     * @return array|mixed|void
     */
    /*public function get(string|array $key): array
    {
        $response = $this->JSONDataGet();
        if($response->status )
        $response = $this->JSONDataFilter(, $key);

        return $response;
    }*/
}