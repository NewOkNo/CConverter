<script setup lang="ts">

//import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
import { ref, onMounted, computed } from 'vue';


interface Props{
  date?: String,
  base?: String,
  to?: String,
  amountFrom?: Number,
}

const props = withDefaults(defineProps<Props>(), {
  date: null,
  base: 'EUR',
  to: 'USD',
  amountFrom: 1,
})

const date = ref(props.date)
const base = ref(props.base)
const to = ref(props.to)
const amountFrom = ref(props.amountFrom)

const amountTo = ref(0)

const rates = ref({})

new getExchangeRates(date.value, base.value).getRates().then((res) => {
  rates.value = res
})

function getRounded(value: number | string) {
  if(!value) return 0
  let beforeDot = 0
  let afterDot = 0
  let switcher = false
  let valueStr = value.toString()
  for(var i = 0; i < valueStr.length; i++) {
    if (valueStr.charAt(i) == '.') {
      switcher = true
    } else {
      if (switcher) {
        afterDot += 1
      } else {
        beforeDot += 1
      }
    }
  }
  if(afterDot > 2 && beforeDot > 3) return value.toFixed(2)
  else if(afterDot > 4) return value.toFixed(4)
  else return value
}

function converCurency(event: Event) {
  //let type = event.target.type
  let id = event.target.id.split(':')
  let value = event.target.value

  let mult = (base.value == props.base) ? 1 : 1 / rates.value[base.value]

  if(id[1] == 'input'){
    if(!value || value == '' || value == null) value = '0'
    value = value.replace(/[^0-9.]+/g, '')
    while(value.length > 1 && value.charAt(0)=='0' && value.charAt(1)!='.') value = value.substr(1)
    //value = getRounded(value)
    if(id[0] == 'from') {
      amountFrom.value = value
      amountTo.value = getRounded(mult * value * rates.value[to.value])
    } else {
      amountTo.value = value
      amountFrom.value = getRounded(mult * value / rates.value[to.value])
    }
  }
  else if(id[1] == 'select'){
    amountTo.value = getRounded(mult * amountFrom.value * rates.value[to.value])
  }
}

</script>

<template>
  <div class="converter">
    <form>
      <div class="header">Currency Converter</div>
      <div class="body">
        <div class="from">
          <select class="cur" id="from:select" v-model="base" @change="converCurency">
            <option :value="props.base">{{ props.base }}</option>
            <option v-for="(rate, currency) in rates" :value="currency" @click="converCurency">{{ currency }}</option>
          </select>
          <input class="amount" id="from:input" type="text" v-model="amountFrom" @input="converCurency" maxlength="8">
        </div>
        <div class="symb">-></div>
        <div class="to">
          <input class="amount" id="to:input" type="text" v-model="amountTo" @input="converCurency" maxlength="8">
          <select class="cur" id="to:select" v-model="to" @change="converCurency">
            <option v-for="(rate, currency) in rates" :value="currency">{{ currency }}</option>
          </select>
        </div>
      </div>
      <div class="footer">
        <div class="text">Convertations date:</div>
        <div class="date">{{ date }}</div>
      </div>
    </form>
  </div>
</template>

<style scoped>
.converter {
  background-color: #f8f8f8;
  border-radius: 16px;
  border-left: 1px solid #ccc;
  border-right: 1px solid #ccc;
  width: 100%;
}
.converter div, select {
  display: flex;
  flex-direction: column;
}
.converter .header {
  /*background-color: #e6e6e6;
  border-radius: 16px 16px 0 0;*/
  padding: 16px;
  font-size: 24px;
  font-weight: bold;
}
.converter .body {
  background-color: #f2f2f2;
  border-radius: 16px;
  /*padding: 16px;*/
  display: flex;
  flex-direction: row;
}
.converter .body div, input, select {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  width: 100%;
  /*margin: auto;*/
  /*padding: 16px;*/
}
.converter .body .from {
  left: 0;
}
.converter .body .to {
  right: 0;
}
.converter .body .amount {
  font-size: 14px;
  font-weight: bold;
  display: flex;
  justify-content: center;
  text-align: center;
  align-items: center;
}
.converter .body .cur {
  font-size: 16px;
  font-weight: bold;
  background-color: #f8f8f8;
  padding: 8px;
  display: flex;
  justify-content: center;
}
.converter .body .cur option {
  text-align: center;
}
.converter .body .from .cur {
  border-radius: 0 12px 12px 0;
  border-right: 1px solid #eee;
  /*text-align: left;*/
}
.converter .body .to .cur {
  border-radius: 12px 0 0 12px;
  border-left: 1px solid #eee;
  /*align-items: center;*/
}
.converter .body .symb {
  font-size: 18px;
  font-weight: bold;
  display: flex;
  justify-content: center;
  align-items: center;
}
/*.converter .body .from {
  border-right: 1px solid #ccc;
  width: 50%;
}*/
.converter .footer {
  /*background-color: #e6e6e6;
  border-radius: 0 0 16px 16px;*/
  padding: 16px 24px 8px 16px;
  font-size: 14px;
  display: flex;
  flex-direction: row;
  justify-content: right;
  align-items: end;
}
.converter .footer .date {
  font-size: 18px;
  margin-left: 8px;
}
.converter .footer .text {
  margin-bottom: 2px;
}
input, select{
  background: none;
  border: none;
}
input:focus, select:focus{
  background: white;
  border: 1px solid grey;
}
</style>
