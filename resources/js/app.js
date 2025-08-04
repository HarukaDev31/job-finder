import './bootstrap';
import './services/auth'; // Importar para inicializar interceptores
import './interceptors/authInterceptor';
import './interceptors/errorInterceptor';
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import { ValidationProvider, ValidationObserver, extend, localize } from 'vee-validate'
import { required, email, min, confirmed } from 'vee-validate/dist/rules'

// Mensajes de validación en español
const messages = {
  es: {
    fields: {
      email: 'El campo email es obligatorio',
      password: 'El campo contraseña es obligatorio',
      'tipo de documento': 'El tipo de documento es obligatorio',
      'número de documento': 'El número de documento es obligatorio',
      nombres: 'Los nombres son obligatorios',
      apellidos: 'Los apellidos son obligatorios',
      'fecha de nacimiento': 'La fecha de nacimiento es obligatoria',
      'confirmar contraseña': 'La confirmación de contraseña es obligatoria'
    },
    validations: {
      required: 'El campo {_field_} es obligatorio',
      email: 'El campo {_field_} debe ser un email válido',
      min: 'El campo {_field_} debe tener al menos {length} caracteres',
      confirmed: 'La confirmación de contraseña no coincide'
    }
  }
}

// Configurar idioma español
localize('es', messages.es)

// Extender las reglas de validación
extend('required', required)
extend('email', email)
extend('min', min)
extend('confirmed', confirmed)

// Registrar componentes de validación globalmente
Vue.component('ValidationProvider', ValidationProvider)
Vue.component('ValidationObserver', ValidationObserver)

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
