import { createRouter, createWebHistory } from "vue-router";
//import HomeView from "../views/HomeView.vue";
import ExchangeRatesView from "../views/ExchangeRatesView.vue";
import AboutView from "../views/AboutView.vue";

/*async function getExchangeRates(to) {
  if (!to.data.rates)
    return { data: { rates: await fetch("https://api.npms.io/v2/search?q=vue") } };
}*/

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: ExchangeRatesView,
      //beforeEnter: [getExchangeRates],
    },
    {
      path: "/about",
      name: "about",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: AboutView,
    },
  ],
});

export default router;
