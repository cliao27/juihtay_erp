<template>
  <b-container fluid>
    <b-row>
      <b-table 
      striped 
      hover 
      borderless 
      small
       dark
       v-bind:fields="fields"
        v-bind:items="appDocument.order_item"
        >
        <!-- A virtual column -->
        <template v-slot:cell(index)="dataList">{{ dataList.index+1 }}</template>
      </b-table>
    </b-row>
  </b-container>
</template>


<!--
	***

	VUE scripts

	***
-->
<script>
import { mapGetters } from "vuex";
import { store } from "@/store/store";
const namespaced = "order";

export default {
  data() {
    return {
      search: "",
      fields: [
        { key: "index", label: "x" },
        "jwn",
        "SKU_code",
        "SKU_customer",
        "print_type",
        "order_qty",
        "work_qty",
        "due_date",
        "work_type",
        "work_progress"
      ]
    };
  },
  mounted() {
    store.appDocument;
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
