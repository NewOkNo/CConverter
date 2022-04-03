<script setup lang="ts">
import ExchangeRatesTable from "@/components/ExchangeRatesTable.vue";
import ExchangeRatesConverter from "@/components/ExchangeRatesConverter.vue";

import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
import { ref, onMounted } from 'vue'

const date = (new getExchangeRates(null)).date;  // will get last possible date if null
const dateMin = "1999-01-01"  // rn backends minimum is 1999-01-01
const dateMax = (new getExchangeRates(null)).date
const base = 'EUR';
const to = 'USD';
const amountFrom = 1;

/*const data = ref({date: null, base: null, rates: {} });

onMounted (() => {
  new getExchangeRates(data.date, data.base)
    .getRates()
    .then((res) => (data.value = res));
});*/

/*new getExchangeRates(this.date, this.base)
      .getRates()
      .then((res) => (this.data = res));*/

/*export default defineComponent({
  components: { ExchangeRatesTable, ExchangeRatesConverter },
  data() {
    return {
      date: null,
      base: null,
      data: {date: null, base: null, rates: {}},
    };
  },
  created() {
    new getExchangeRates(this.date, this.base)
      .getRates()
      .then((res) => (this.data = res));
  },
});*/
//:data="data"
</script>

<template>
  <main>
    <ExchangeRatesConverter :date="date" :dateMin="dateMin" :dateMax="dateMax" :base="base" :to="to" :amountFrom="amountFrom" />
    <ExchangeRatesTable :date="date" :dateMin="dateMin" :dateMax="dateMax" :base="base" />
  </main>
</template>
