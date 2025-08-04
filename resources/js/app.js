import './bootstrap';
import './services/auth';
import './interceptors/authInterceptor';
import './interceptors/errorInterceptor';
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';

import App from './components/layout/App.vue';
import router from './router';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

Vue.use(BootstrapVue);

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App)
});
