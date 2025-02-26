import './bootstrap';
import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import Calendar from "./components/Calendar.vue";

const routes = [{ path: "/", component: Calendar }];
const router = createRouter({ history: createWebHistory(), routes });

createApp({}).use(router).mount("#app");
