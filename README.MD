# Curency Converter

## How To

### backend
* Run `composer update` to download project packages
* Add your [exchangeratesapi](https://exchangeratesapi.io/) key in the .env as `EXCHANGE_RATES_API_KEY=$key` for it source to work (not necessary)
* Run `php -S localhost:8000 -t public` to run backend server
* Go to `http://localhost:8000/cconverter/` or `http://localhost:8000/cconverter/$date/$baseCurrency` to see the result

### frontend
* Run `npm i` to download project packages
* Run `npm run dev` to run frontend server
* Go to `http://localhost:3000/` to see the result

## For devs
* You can add another exchange rates soures in the `$requestSources` variable in the `ExchangeRates.php` file and use custom pattern for `json`, `xml` or `html` data types. If you want to add another type then create new function as `getData$type`
