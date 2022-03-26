<script lang="ts">
import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";

export default defineComponent({
  props: {
    base: String
  },
  data(){
    return {
      base: this.base,
      date: 1,
      response: null
    }
  },
  created() {
    //new getExchangeRates(this.date, this.base).getRates().then(res => (this.response = res[1]))
    fetch("http://localhost:8000/cconverter/2022-03-25/EUR").then(res => res.json()).then(data => (this.response = data));
  }
})
/*const props = defineProps<{
  base: string;
}>();
//const emit = defineEmits(["change", "delete"]);
//let date = new Date().toISOString().slice(0, 10);
//import { defineComponent } from "vue";
import { getExchangeRates } from "@/components/scripts/exchangeRates";
//const response = await (new getExchangeRates(null, props.base)).getRates();
const response = "proverka";
//const response2 = await fetch("http://localhost:8000/cconverter/2022-03-25/EUR").then(res => res.json()).then(data => (response = data.code));*/
</script>

<!---import { defineComponent } from "vue";

export default defineComponent({
  props: {
    base: String
  },
  data(){
    return {
      response: "fdx"
    }
  },
  created(){
    fetch("http://localhost:8000/cconverter/2022-03-25/EUR")
      .then(res => res.json())
      .then(data => (this.response = data.code))
  }
  methods: {
    vruh(){
      this.response = await fetch("http://localhost:8000/cconverter/2022-03-25/EUR").then(res => res.json()).then(data => (response = data.code))
    }
  },
})-->

<template>
  <div class="main">
    <div class="header">
      <div class="title">
        <h1>Exchange Rates</h1>
      </div>
      <div class="settings">
        <!--<div class="date" id="date"></div>
        <div class="base" id="base"></div>-->
        <div class="date">{{ response.date }}</div>
        <div class="base">{{ response.base }}</div>
      </div>
    </div>
    <!--<div>
      <table>
        <thead>
          <tr>
            <th>Currency</th>
            <th>Rate</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(rate, currency) in response[1].rates">
            <td>{{ currency }}</td>
            <td>{{ rate }}</td>
          </tr>
        </tbody>
      </table>
    </div>-->
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
  padding: 0 20px;
}
.main .header div {
  display: flex;
  flex-direction: row;
  align-items: center;
  /*justify-content: space-between;
  width: 100%;*/
  padding: 0 20px;
}
.main .header .settings div {
  padding: 0 10px;
}
</style>
