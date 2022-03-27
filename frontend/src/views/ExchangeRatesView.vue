<script setup lang="ts">
import ExchangeRatesTable from "@/components/ExchangeRatesTable.vue";
import ExchangeRatesConverter from "@/components/ExchangeRatesConverter.vue";

import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
import { ref, onMounted } from 'vue'

const data = ref({date: null, base: null, rates: {} });

onMounted (() => {
  new getExchangeRates(data.date, data.base)
    .getRates()
    .then((res) => (data.value = res));
});
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

</script>

<template>
  <main>
    <ExchangeRatesConverter :data="data" />
    <ExchangeRatesTable :data="data" />
  </main>
</template>
