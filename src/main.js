import Vue from 'vue'
// import Vuex from 'vuex'
import axios from 'axios'
import VueRouter from 'vue-router'
import VueAxios from 'vue-axios'
// import VueResource from 'vue-resource'
// axios.defaults.withCredentials = true
import App from './App.vue'
import Routes from './routes/routes'
import { store } from './store/store'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Registering routing functions
Vue.use(VueRouter);
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(VueAxios, axios)

const apiURL = "http://192.168.103.160:5000/";
// const apiURL = "http://192.168.1.60:5000/";
// const apiURL = "http://192.168.5.132:5000/";


axios.defaults.baseURL = process.env.NODE_ENV === 'development' ? apiURL : apiURL;

// Register routes
const router = new VueRouter({
    routes: Routes,
    mode: 'history'
});


new Vue({
  el: '#app',
  store: store,
  render: h => h(App),
  router: router
})