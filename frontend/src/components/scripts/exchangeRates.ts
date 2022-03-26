class getExchangeRates {
  date: string;
  base: string;
  url = "http://localhost:8000/cconverter/";
  constructor(date?: string, base?: string) {
    if (date) this.date = date;
    else {
      const date = new Date();
      date.setDate(date.getDate() - 1);
      this.date = date.toISOString().slice(0, 10);
    }

    if (base) this.base = base;
    else this.base = "EUR";
  }
  /*getRates() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (this.status == 200) {
          document.getElementById("date").innerHTML = JSON.parse(this.responseText).date;
          document.getElementById("base").innerHTML = JSON.parse(this.responseText).base;
        }
      }
    xhttp.open("GET", (this.url + this.date + "/" + this.base), true);
    xhttp.send();
    return(xhttp);
  }*/
  //// fetch() does ajax requests so it's probably counts ////
  async getRates() {
    //return [200, "bruh"]
    let response;
    fetch(this.url + this.date + "/" + this.base).then(res => res.json()).then(rs => (response = rs));
    return response;
    /*await fetch(this.url + this.date + "/" + this.base).then((res) => {
      if (res.ok) return [200, res.json()];
      else return [res.status, res.json()];
    }).catch((err) => {
      return [500, err];
    });*/
  }
}
export { getExchangeRates };
