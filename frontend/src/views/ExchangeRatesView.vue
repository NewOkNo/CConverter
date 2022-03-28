<script setup lang="ts">
import ExchangeRatesTable from "@/components/ExchangeRatesTable.vue";
import ExchangeRatesConverter from "@/components/ExchangeRatesConverter.vue";

import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
import { ref, onMounted } from 'vue'

const date = null;    // will get last possible date if null
const base = 'EUR';
const to = 'USD';
const amountFrom = 0;

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
    <ExchangeRatesConverter :date="date" :base="base" :to="to" :amountFrom="amountFrom" />
    <!--<ExchangeRatesTable :data="data" />-->
  </main>
</template>
