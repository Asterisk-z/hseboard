import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import { router } from './router';
import vuetify from './plugins/vuetify';
import '@/scss/style.scss';
import PerfectScrollbar from 'vue3-perfect-scrollbar';
import VueApexCharts from 'vue3-apexcharts';
import VueTablerIcons from 'vue-tabler-icons';
import { fakeBackend } from '@/utils/helpers/fake-backend';
import 'vue3-carousel/dist/carousel.css';
// import DatetimePicker from 'vuetify-datetime-picker'
//Mock Api data
import './_mockApis';
import "./registerServiceWorker";

import VCalendar from 'v-calendar';
import VueRecaptcha from 'vue3-recaptcha-v2';
import Maska from 'maska';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
// print
// import print from 'vue3-print-nb';
// Table
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
//i18
import { createI18n } from 'vue-i18n';
import messages from '@/utils/locales/messages';


import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import ClipLoader from 'vue-spinner/src/ClipLoader.vue'
import Vue3Toasity, { type ToastContainerOptions } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import '@/assets/css/toast-wrapper.css'
import GoogleSignInPlugin from "vue3-google-signin"

import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
const i18n = createI18n({
    locale: 'en',
    messages: messages,
    silentTranslationWarn: true,
    silentFallbackWarn: true
});

const app = createApp(App);
// fakeBackend();
app.use(router);
app.component('EasyDataTable', Vue3EasyDataTable);
app.use(PerfectScrollbar);
app.use(createPinia());
app.use(VCalendar, {});
app.use(VueTablerIcons);
// app.use(print);
app.use(VueRecaptcha, {
    siteKey: '6LdzqbcaAAAAALrGEZWQHIHUhzJZc8O-KSTdTTh_',
    alterDomain: false // default: false
});
app.use(i18n);
app.use(Maska);
app.use(VueApexCharts);
app.use(vuetify).use(
    Vue3Toasity,
    { autoClose: 3000, multiple: true, limit: 3, newestOnTop : true } as ToastContainerOptions,
)

app.use(GoogleSignInPlugin, {
    clientId: '661088481369-cjnjhm6eijug19q05nn61931p15j2frd.apps.googleusercontent.com',
});
// app.use(DatetimePicker);
app.component('pulse-loader', PulseLoader)
app.component('clip-loader', ClipLoader).mount('#app');
app.component('VueDatePicker', VueDatePicker);
app.component('flat-pickr', flatPickr);
