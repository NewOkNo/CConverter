<?php

namespace Src\Bases;

abstract class Model{

    //// Protected Functions ////

    /**
     * Saves new object to the JSON file.
     *
     * @param string
     * @return array
     * @throws \Exception
     */
    protected function JSONDataPut($data): array
    {
        try{
            $fp = fopen($this->jsonDir, 'w');
            if($fp) // TODO: base for keys
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
     * @param $json
     * @return array
     */
    protected function JSONDataSort($json, $key, $value): array
    {
        foreach ($json as $object){
            if()
        }
        return [300, ""];
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
     * @param $key
     * @param $searchable
     * @return array
     * @throws \Exception
     */
    public function get($key, $searchable): array
    {
        $json = $this->JSONDataGet();
        if($json[0] != 200){ return $json; }
        return [];
    }
}