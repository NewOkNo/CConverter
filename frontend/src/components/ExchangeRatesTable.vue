<script setup lang="ts">

import { getExchangeRates, getRounded } from "@/components/scripts/exchangeRates";
import { ref, onMounted, computed } from 'vue';


interface Props{
  date?: String,
  dateMin?: String,
  dateMax?: String,
  base?: String,
}

const props = withDefaults(defineProps<Props>(), {
  date: null,
  dateMin: "1999-01-01",
  dateMax: (new getExchangeRates(null)).date,
  base: 'EUR',
})

const date = ref(props.date)
const dateMin = ref(props.dateMin)
const dateMax = ref(props.dateMax)
const base = ref(props.base)

const rates = ref({})

new getExchangeRates(date.value, base.value).getRates().then((res) => {
  rates.value = res
})

function converCurency(event: Event) {
  let id = event.target.id
  let value = event.target.value

  if(id=='date'){
    date.value = value
  }
  else if(id=='base'){
    base.value = value
  }

  new getExchangeRates(date.value, base.value).getRates().then((res) => {
    rates.value = res
  })
}

</script>

<template>
  <div class="main">
    <div class="header">
      <div class="title">
        <h1>Exchange Rates</h1>
      </div>
      <div class="settings">
        <input class="date" id="date" type="date" v-model="date" @change="converCurency" min="{{ dateMin }}" :max="dateMax">
        <select class="cur" id="base" v-model="base" @change="converCurency">
          <option :value="props.base">{{ props.base }}</option>
          <option v-for="(rate, currency) in rates" :value="currency">{{ currency }}</option>
        </select>
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
          <tr v-for="(rate, currency) in rates">
            <td>{{ currency }}</td>
            <td>{{ getRounded(rate) }}</td>
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
.main .header div, input, select {
  display: flex;
  flex-direction: row;
  align-items: center;
  /*justify-content: space-between;
  width: 100%;*/
}
.main .header .settings div, input, select {
  padding: 0 10px;
}
input, select{
  background: none;
  border: none;
}
input:focus, select:focus{
  background: white;
  border: 1px solid grey;
}
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="date"]::-webkit-inner-spin-button{
    margin: auto;
    font-size: 84%;
}
</style>
