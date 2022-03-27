<script setup lang="ts">

//import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
import { ref, onMounted, computed } from 'vue';

interface Props{
  data?: Object
}

const props = withDefaults(defineProps<Props>(), {
  data: {date: null, base: null, rates: {}},
})

const cCodeFrom = ref('EUR')
const cCodeTo = ref('USD')
const cValueFrom = ref(1)
const cValueTo = ref(props.data.rates[cCodeTo])

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
  //if(afterDot < 2) return number
  console.log(value)
  if(afterDot > 2 && beforeDot > 3) return value.toFixed(2)
  else if(afterDot > 4) return value.toFixed(4)
  else return value
}

//cValueFrom.value = computed(() => cValueFrom.value.toFixed(fixNum))

//cValueTo.value = cValueFrom.value * props.data.rates[cCodeTo.value]

/*onMounted (() => {
  if(props.data.date){
    cValueTo.value = cValueFrom.value * props.data.rates[cCodeTo.value]
  }
  //cValueTo.value = cValueFrom.value * props.data.rates[cCodeTo.value]
})*/

function converCurency(event: Event) {
  let type = event.target.type
  let id = event.target.id
  let value = event.target.value

  if(type == 'text'){
    if(!value || value == '' || value == null) value = '0'
    value = value.replace(/[^0-9.]+/g, '')
    while(value.length > 1 && value.charAt(0)=='0' && value.charAt(1)!='.') value = value.substr(1)
    //value = getRounded(value)
    if(id == 'from') {
      cValueFrom.value = value
      cValueTo.value = getRounded(value * props.data.rates[cCodeTo.value])
    } else {
      cValueTo.value = value
      cValueFrom.value = getRounded(value / props.data.rates[cCodeTo.value])
    }
  }
}
//cValueFrom*data.rates.(cCodeTo.value)
</script>

<template>
  <div class="converter">
    <form>
      <div class="header">Currency Converter</div>
      <div class="body">
        <div class="from">
          <div class="cur">{{ data.base }}</div>
          <input class="amount" id="from" type="text" v-model="cValueFrom" @input="converCurency" maxlength="8">
        </div>
        <div class="symb">-></div>
        <div class="to">
          <input class="amount" id="to" type="text" v-model="cValueTo" @input="converCurency" maxlength="8">
          <div class="cur">USD</div>
        </div>
      </div>
      <div class="footer">
        <div class="text">Convertations date:</div>
        <div class="date">{{ data.date }}</div>
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
.converter div {
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
.converter .body div, input {
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
input{
  background: none;
  border: none;
}
input:focus {
  background: white;
  border: 1px solid grey;
}
</style>
