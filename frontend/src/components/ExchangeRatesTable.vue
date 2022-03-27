<script setup lang="ts">

//import { ref } from 'vue'
import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";

/*export default defineComponent({
  props: {
    base: String,
  },
  data() {
    return {
      date: null,
      data: {date: null, rates: {}, base: null},
    };
  },
  created() {
    new getExchangeRates(this.date, this.base)
      .getRates()
      .then((res) => (this.data = res));
  },
});*/

interface Props{
  data?: Object
}

const props = withDefaults(defineProps<Props>(), {
  data: {date: null, base: null, rates: {}},
})

//const {data = {date: null, base: null, rates: {}}} = defineProps<Props>()

/*const props = defineProps({
  data: Object,
});*/

/*let date = ref(null);
let data = ref({date: null, rates: {}, base: null});

new getExchangeRates(date, props.base)
      .getRates()
      .then((res) => (data = res));*/

</script>

<template>
  <div class="main">
    <div class="header">
      <div class="title">
        <h1>Exchange Rates</h1>
      </div>
      <div class="settings">
        <!--<div class="date" id="date"></div>
        <div class="base" id="base"></div>-->
        <div class="date">{{ data.date }}</div>
        <div class="base">{{ data.base }}</div>
      </div>
    </div>
    <div class="table">
      <table>
        <thead>
          <tr>
            <th>Currency</th>
            <th>Rate</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(rate, currency) in data.rates">
            <td>{{ currency }}</td>
            <td>{{ rate }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
/*.main{
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}*/
.main .header {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}
.main .header div {
  display: flex;
  flex-direction: row;
  align-items: center;
  /*justify-content: space-between;
  width: 100%;*/
}
.main .header .settings div {
  padding: 0 10px;
}

.main .table {
}
</style>
