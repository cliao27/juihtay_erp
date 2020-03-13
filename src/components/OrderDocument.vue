<template>
  <div>
    <b-row>
      <b-col lg="6" sm="12">
        <b-form-group
          id="customer"
          label-cols-sm="4"
          label-cols-lg="2"
          label="customer"
          label-for="input-horizontal"
        >
          <b-form-input id="input-horizontal" v-model="appDocument.customer"></b-form-input>
        </b-form-group>

        <b-form-group
          id="order_number"
          label-cols-sm="4"
          label-cols-lg="2"
          label="order_number"
          label-for="input-horizontal"
        >
          <b-form-input id="input-horizontal" v-model="appDocument.order_number"></b-form-input>
        </b-form-group>

        <b-form-group
          id="product_codes"
          label-cols-sm="4"
          label-cols-lg="2"
          label="product_codes"
          label-for="input-horizontal"
        >
          <b-form-tags input-id="tags-basic" v-model="appDocument.product_codes" class="mb-2"></b-form-tags>
          <p>Value: {{ appDocument.product_codes }}</p>
        </b-form-group>

        <b-form-group
          id="order_note"
          label-cols-sm="4"
          label-cols-lg="2"
          label="order_note"
          label-for="input-horizontal"
        >
          <b-form-textarea input-id="tags-basic" v-model="appDocument.order_note" rows="4"></b-form-textarea>
        </b-form-group>
      </b-col>
      <b-col lg="3" sm="6">
        <b-row>
          <b-col>{{appDocument._id}}</b-col>
        </b-row>
        <b-row>
          <b-col>ID</b-col>
          <b-col>{{appDocument.jon}}</b-col>
        </b-row>

        <b-row>
          <b-col>Progress</b-col>
          <b-col>{{appDocument.order_progress}}</b-col>
        </b-row>
        <b-row>
          <b-col>Revision</b-col>
          <b-col>{{appDocument.order_revision}}</b-col>
        </b-row>
        <b-row>
          <b-col>Items</b-col>
          <b-col>{{appDocument.order_item_completed}} / {{appDocument.order_item_count}}</b-col>
        </b-row>
        <b-row>
          <b-col>Order Datetime</b-col>
          <b-col>{{appDocument.order_datetime}}</b-col>
        </b-row>
        <b-row>
          <b-col>Ack Datetime</b-col>
          <b-col>{{appDocument.ack_datetime}}</b-col>
        </b-row>
        <b-row>
          <b-col>Order Entry</b-col>
          <b-col>{{appDocument.order_clerk}}</b-col>
        </b-row>
      </b-col>
      <b-col lg="3" sm="6">
        <b-row>
          <b-list-group>
            <div v-for="log in appDocument.order_log" :key="log.seq">
              <b-list-group-item href="#" class="flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                  <small>{{log}}</small>
                </div>
              </b-list-group-item>
            </div>
          </b-list-group>
        </b-row>
      </b-col>
      <b-col sm="12" lg="12">
        <div>
          <b-card no-body>
            <b-tabs pills card>
              <div v-for="product in appDocument.product_codes" :key="product">
                <b-tab :title="product">
                  <div>
                    <b-input-group>
                      <b-form-group
                        id="fieldset-1"
                        label="รหัสสินค้า 產品編號"
                        :description="`${product}-${order_sku_jtcode}`"
                        label-for="input-1"
                        :invalid-feedback="invalidFeedback"
                        :valid-feedback="validFeedback"
                        :state="state"
                      >
                        <b-form-input id="input-1" v-model="order_sku_jtcode" :state="state" trim></b-form-input>
                      </b-form-group>

                      <b-form-group
                        id="fieldset-1"
                        :description="order_sku_jtspec"
                        :label="`${product}-${order_sku_jtcode} ${order_sku_jtspec}`"
                        label-for="input-1"
                        :invalid-feedback="invalidFeedback"
                        :valid-feedback="validFeedback"
                        :state="state"
                      >
                        <b-form-input id="input-1" v-model="order_sku_jtspec" :state="state" trim></b-form-input>
                      </b-form-group>

                      <b-form-group
                        id="fieldset-1"
                        label="จำนวน 訂單數量"
                        :description="`QTY ${order_qty}`"
                        label-for="input-1"
                        :invalid-feedback="invalidFeedback"
                        :valid-feedback="validFeedback"
                        :state="state"
                      >
                        <b-form-input id="input-1" small v-model="order_qty" :state="state" trim></b-form-input>
                      </b-form-group>
                      <b-form-group
                        id="fieldset-1"
                        :description="order_due_date"
                        label="กำหนดส่ง出貨日期"
                        label-for="input-1"
                        :invalid-feedback="invalidFeedback"
                        :valid-feedback="validFeedback"
                        :state="state"
                      >
                        <b-form-datepicker
                          id="example-datepicker"
                          v-model="order_due_date"
                          locale="th"
                          class="mb-2"
                        ></b-form-datepicker>
                      </b-form-group>

                      <b-form-group
                        id="fieldset-1"
                        :description="selected"
                        label="Type"
                        label-for="input-1"
                        :invalid-feedback="invalidFeedback"
                        :valid-feedback="validFeedback"
                        :state="state"
                      >
                        <b-form-select v-model="selected" :options="options"  class="mb-2"></b-form-select>
                      </b-form-group>
                    </b-input-group>
                  </div>
                </b-tab>
              </div>
            </b-tabs>
          </b-card>
        </div>
        <b-card no-body>
          <b-tabs v-model="tabIndex" small card fill>
            <b-tab :title="`ALL: ${appDocument.order_item_count}`">
              <order-work></order-work>
            </b-tab>
            <b-tab :title="`Work: ${orderItemWorking}`">
              <order-work display="work"></order-work>
            </b-tab>
            <b-tab :title="`Done: ${appDocument.order_item_completed}`">
              <order-work display="done"></order-work>
            </b-tab>
            <b-tab title="Shipping">            </b-tab>
            <b-tab title="Scanned PDF"></b-tab>
            <b-tab title="JSON">
              <vue-json-pretty :data="appDocument"></vue-json-pretty>
            </b-tab>
          </b-tabs>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>


<!--
	***

	VUE scripts

	***
-->
<script>
import { mapActions, mapGetters } from "vuex";
import { store } from "@/store/store";

const namespaced = "order";

import OrderWork from "./OrderWork.vue";
// import OrderEntry from "./form/OrderEntry.vue";
import VueJsonPretty from "vue-json-pretty";

export default {
  components: {
    "order-work": OrderWork,
    // "order-entry": OrderEntry,
    VueJsonPretty
  },
  data() {
    return {
      tabIndex: 0,
      order_qty: "",
      order_sku_jtcode: "",
      order_sku_jtspec: "",
      order_sku_customer: "",
      order_due_date: "",
      order_type: "",
      selected: null,
        options: [
          { value: null, text: 'Please select an option' },
          { value: 'a', text: 'This is First option' },
          { value: 'b', text: 'Selected Option' },
          { value: { C: '3PO' }, text: 'This is an option with object value' },
          { value: 'd', text: 'This one is disabled', disabled: true }
        ]
    };
  },
  mounted() {
    console.log("Fetch Document", this.$route.path);
    store.dispatch(namespaced + "/FETCH_DOCUMENT", this.$route.path);
  },
  methods: {
    ...mapActions(namespaced, ["FETCH_DOCUMENT"])
  },
  computed: {
    ...mapGetters(namespaced, ["appDocument"]),
    orderItemWorking: function() {
      return (
        this.appDocument.order_item_count -
        this.appDocument.order_item_completed
      );
    }
    // orderDocument: function() {
    //   var orderDocument = this.appDocument;
    //   this.$delete(orderDocument, 'order_item');
    //   return orderDocument;
    // }
  }
};
</script>



<!--
	***

	BELOW IS STYLING OF WEBPAGE SCOPED
	
	***
!-->
<style scoped>
</style>
