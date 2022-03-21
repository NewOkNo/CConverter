<?php

namespace Src\Bases;

abstract class Model{

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
        try{
            $fp = fopen($this->jsonDir, 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
        }catch (\Exception $e){
            return [500, "Failed to add data to the json file"];
        }
        return [200, true];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @return array
     */
    protected function JSONDataGet(): array
    {
        try{
            $rawjson = file_get_contents($this->jsonDir);
            $json = json_decode($rawjson);
        }catch (\Exception $e){
            return [500, "Failed to read data from the json file"];
        }
        return [200, $json];
    }

    /**
     * Reads JSON file and returning JSON array.
     *
     * @param array $json
     * @param string|array $key
     * @return array|mixed|void
     */
    protected function JSONDataFilter(array $json, string|array $key): array
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
    }

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
    public function get(string|array $key): array
    {
        $response = $this->JSONDataFilter($this->JSONDataGet(), $key);

        return $response;
    }
}