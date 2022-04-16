import currencyNames from "@/assets/currencyNames.json";

class getExchangeRates {
  date: string;
  base: string;
  to: string;
  url = "http://localhost:8000/cconverter/";
  cachedRates: Object = {};
  constructor(date?: string, base?: string, to?: string) {
    if (date) this.date = date;
    else {
      // shows todays date after 13:00
      const date = new Date();
      const updateTime = new Date();
      updateTime.setHours(0, 0, 0, 0);
      updateTime.setTime(updateTime.getTime() + 46800000);
      console.log(date + " | " + updateTime);
      if (date.getTime() < updateTime.getTime())
        date.setDate(date.getDate() - 1);
      this.date = date.toISOString().slice(0, 10);
    }

    if (base) this.base = base;
    else this.base = "EUR";

    if (to) this.to = to;
    else this.to = "";

    this.url += this.date + "/" + this.base;
    //if(this.to) this.url += '/'+this.to;
  }
  async getRates() {
    const response = fetch(this.url, { cache: "force-cache" })
      .then((res) => {
        if (res.ok) return res.json();
        else throw new Error("Network response was not OK");
      })
      .then((json) => {
        if (json.date != this.date || json.base != this.base)
          throw new Error(
            "Wrong data " +
              json.date +
              " " +
              json.base +
              " " +
              this.date +
              " " +
              this.base
          );
        else {
          if (!json.rates[this.base]) json.rates[this.base] = 1;

          // sorting
          json.rates = Object.keys(json.rates)
            .sort()
            .reduce((r, key) => {
              (r as any)[key] = json.rates[key];
              return r;
            }, {});

          if (this.to) {
            if (json.rates[this.to]) return json.rates[this.to];
          } else return json.rates;
        }
      });
    return response;
  }
}

function getRounded(value: number | string): number {
  if (typeof value === "string") value = parseFloat(value);
  if (!value) return 0;
  let beforeDot = 0;
  let afterDot = 0;
  let switcher = false;
  const valueStr = value.toString();
  for (let i = 0; i < valueStr.length; i++) {
    if (valueStr.charAt(i) == ".") {
      switcher = true;
    } else {
      if (switcher) {
        afterDot += 1;
      } else {
        beforeDot += 1;
      }
    }
  }
  if (afterDot > 2 && beforeDot > 3) return Number.parseFloat(value.toFixed(2));
  else if (afterDot > 4) return Number.parseFloat(value.toFixed(4));
  else return value;
}

function getCurrencyName(code: string) {
  //let json = JSON.parse(dictionaryPath)
  const curName = (currencyNames as any)[code];

  if (!curName) return code;
  return curName;
}

export { getExchangeRates, getRounded, getCurrencyName };
//export default getExchangeRates;
