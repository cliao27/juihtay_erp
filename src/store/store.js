import Vue from 'vue'
import Vuex from 'vuex'

import customer from "@/store/customer"
import product from "@/store/product"
import order from "@/store/order"
import work from "@/store/work"
import material from "@/store/material"

// Make sure to call Vue.use(Vuex) first if using a module system
Vue.use(Vuex)

export const store = new Vuex.Store({
  namespaced:true,
  modules: {
    customer,
    product,
    order,
    work,
    material
  },
  state: {
    // productDocument: []
  },
  getters: {
    // productDocument: state => {
    //   return state.productDocument;
    // },
  },
  actions: {
    // loadProductDocument({ commit }, db) {
    //   console.log(db);
    //   axios
    //     .get(db.collection)
    //     .then(response => response.data)
    //     .then(data => {
    //       console.log("data by vuex axios");
    //       console.log(data);
    //       commit('SET_PRODUCT_DOCUMENT', data);
    //     })
    // },
  },
  mutations: { //setters
    // SET_PRODUCT_DOCUMENT(state, data) {
    //   this.state.productDocument = data;
    // },
  },
  strict: process.env.NODE_ENV !== 'production'
})


export default store