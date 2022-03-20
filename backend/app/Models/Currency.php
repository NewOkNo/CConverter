<?php

namespace App\Models;

use src\Bases\Model;

class Currency extends Model
{
    /**
     * Currency code of the model.
     *
     * @var string
     */
    protected $code;

    /**
     * Gets exchange rate of the model.
     *
     * @var double
     */
    public function getExchangeRate($base='EUR'){
        $exRate = 0.0;
        return $exRate;
    }

    /**
     * Get all of the models from the database.
     *
     * @return array
     */
    public static function all()
    {
        return [];
    }
}