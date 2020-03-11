<template>
  <div>
    <b-row>
      <b-col cols="12" lg="9">
        <b-card no-body>
          <b-tabs v-model="tabIndex" small card fill>
            <b-tab title="JSON">
              <vue-json-pretty :data="appDocument"></vue-json-pretty>
            </b-tab>
            <b-tab title="Print Out">
              <!-- <work-print-out :appDocument="appDocument"></work-print-out> -->
              <work-print-out :ptn="appDocument.ptn"></work-print-out>
            </b-tab>
            <b-tab title="Scanned PDF"></b-tab>
            <b-tab title="Shipping">
              <shipping-record :records="appDocument.shipping_record"></shipping-record>
            </b-tab>
            <b-tab title="Logs">
              <work-log :records="appDocument.work_log"></work-log>
            </b-tab>
          </b-tabs>
        </b-card>
      </b-col>
      <b-col cols="4" lg="3" class="d-print-none">
        <b-list-group>
          <div v-for="log in appDocument.work_log" :key="log.seq">
            <b-list-group-item href="#" class="flex-column align-items-start">
              <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">{{log.station}} {{log.station_group}} {{log.station_id}}</h6>
                <small>{{log.start_datetime}}</small>
              </div>
              <small class="text-muted">{{log.description}}</small>
            </b-list-group-item>
          </div>
        </b-list-group>
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

const namespaced = "work";

import VueJsonPretty from "vue-json-pretty";
import ShippingRecord from "./product/ShippingRecord";
import WorkLog from "./product/WorkLog";
import WorkPrintOut from "./product/WorkPrintOut.vue";

export default {
  components: {
    VueJsonPretty,
    "shipping-record": ShippingRecord,
    "work-log": WorkLog,
    "work-print-out": WorkPrintOut,
  },
  data() {
    return {
      tabIndex: 1
    };
  },
  mounted() {
    // console.log("Fetch Document", this.databasePath);
    store.dispatch(namespaced + "/FETCH_DOCUMENT", this.$route.path);
  },
  methods: {
    ...mapActions(namespaced, ["FETCH_DOCUMENT"])
  },
  computed: {
    ...mapGetters(namespaced, ["appDocument"])
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
