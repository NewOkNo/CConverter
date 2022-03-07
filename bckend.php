<?php

require_once 'devTools.php';
use DevTools\DotEnv;

function CExchangeUnCached($date){
    
    (new DotEnv(__DIR__ . '/.env'))->load();
    
    //YYYY-MM-DD 
    $request = "http://api.exchangeratesapi.io/v1/".$date."?access_key=".getenv("EXCHANGE_RATES_API_KEY")."&base=EUR";
    $response  = file_get_contents($request);
    return $response;

}

function CExchangeCached($date){



}

function CConverter($amount=1.0, $from="EUR", $to="USD", $date=Null){
    if($date){

    }
}

?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

</body>
</html>

