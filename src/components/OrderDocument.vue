<template>
  <div>
    <b-row>
      <b-col cols="12" lg="9">
        <b-card no-body>
          <b-tabs v-model="tabIndex" small card fill>
            <b-tab title="JSON">
              <vue-json-pretty :data="appDocument"></vue-json-pretty>
              <order-work></order-work>
            </b-tab>
            <b-tab title="Print Out">
            </b-tab>
            <b-tab title="Scanned PDF"></b-tab>
            <b-tab title="Shipping"></b-tab>
            <b-tab title="Logs"></b-tab>
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
import VueJsonPretty from "vue-json-pretty";

export default {
  components: {
    "order-work": OrderWork,
    VueJsonPretty
  },
  data() {
    return {
    tabIndex: 0,
      databasePath: this.$route.path,
      list: {
        fields: [
          "รหัสสินค้า",
          "2",
          "รายการสินค้า",
          "4",
          "ชนิดกระดาษ",
          "สินค้าคงเหลือ",
          "ตระกร้า",
          "รูปแบบการพิมพ์"
        ]
      }
    };
  },
  mounted() {
    console.log("Fetch Document", this.databasePath);
    store.dispatch(namespaced + "/FETCH_DOCUMENT", this.databasePath);
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
