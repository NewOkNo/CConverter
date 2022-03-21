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
     * @throws \Exception
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
     * @param string $key
     * @param string $value
     * @return array
     */
    protected function JSONDataFilter(array $json, string $key, string $value): array
    {
        $newjson = [];
        if(!$json[0][$key])return [400, "Key in not exists!"];
        foreach ($json as $object){
            if($object[$key] == $value) $newjson[] = $object;
        }
        if($newjson) return [200, $newjson];
        else return [300, "Objects with such key value not found!"];
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
     * @param string $key
     * @param string|int $value
     * @return array
     * @throws \Exception
     */
    public function get(string $key, string|int $value): array
    {
        $json = $this->JSONDataFilter($this->JSONDataGet(), $key, strval($value));

        if($json[0] != 200){ return $json; }
        else return $json;
    }
}