import { createApp } from "vue";
import { createPinia } from "pinia";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "nprogress/nprogress.css";
import "simplebar-vue/dist/simplebar.min.css"; // trigger HMR
import "./style.css";
import App from "./App.vue";
import router from "./router";
import DataTableScroll from "./components/ui/DataTableScroll.vue";
import TablePagination from "./components/ui/TablePagination.vue";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 20,
  newestOnTop: true,
});
app.component("DataTableScroll", DataTableScroll);
app.component("TablePagination", TablePagination);
app.mount("#app");
