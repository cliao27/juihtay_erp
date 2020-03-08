<template>
  <div>
    <div>
      Sorting By:
      <b>{{ sortBy }}</b>, Sort Direction:
      <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
    </div>
    <b-col lg="4" class="my-1">
      <b-form-group
        label="Filter"
        label-cols-sm="3"
        label-align-sm="right"
        label-size="sm"
        label-for="filterInput"
        class="mb-0"
      >
        <b-input-group size="sm">
          <b-form-input
            v-model="filter"
            type="search"
            id="filterInput"
            placeholder="Type to Search"
          ></b-form-input>
          <b-input-group-append>
            <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
          </b-input-group-append>
        </b-input-group>
      </b-form-group>
    </b-col>
    <b-col sm="2" md="3" class="my-1">
      <b-form-group
        label="Per page"
        label-cols-sm="4"
        label-cols-md="3"
        label-cols-lg="2"
        label-align-sm="right"
        label-size="sm"
        label-for="perPageSelect"
        class="mb-0"
      >
        <b-form-select v-model="perPage" id="perPageSelect" size="sm" :options="pageOptions"></b-form-select>
      </b-form-group>
    </b-col>

    <b-col sm="4" md="4" class="my-1">
      <b-pagination
        v-model="currentPage"
        :total-rows="list.length"
        :per-page="perPage"
        align="fill"
        size="sm"
        class="my-0"
      ></b-pagination>
    </b-col>

    <b-table
      striped
      hover
      small
      show-empty
      foot-clone
      no-footer-sorting
      head-variant="dark"
      :fields="fields"
      :items="list"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :filterIncludedFields="filterOn"
    >
      <template v-slot:cell(jon)="data">
        <!-- `data.value` is the value after formatted by the Formatter -->
        <a :href="`${databasePath}/${data.value}`">{{ data.value }}</a>
      </template>
      <template v-slot:cell(customer)="data">
        <!-- `data.value` is the value after formatted by the Formatter -->
        <a :href="`${databasePath}/customer/${data.value}`">{{ data.value }}</a>
      </template>
      <!-- A virtual column -->
      <template v-slot:cell(index)="dataList">{{ dataList.index+1 }}</template>
    </b-table>
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

export default {
  data() {
    return {
      pageList:[],
      filter: null,
      filterOn: ["order_number", "customer"],
      totalRows: 1,
      currentPage: 1,
      perPage: 100,
      pageOptions: [100, 500, 2000],
      sortBy: "order_datetime",
      sortDesc: true,

      databasePath: this.$route.path,
      fields: [
        { key: "index", label: "x" },
        {
          key: "order_datetime",
          formatter: value => {
            return value.slice(0, 10);
          }
        },
        { key: "jon" },
        { key: "customer" },
        { key: "order_number" },
        { key: "order_item_count" },
        { key: "order_item_completed" },
        { key: "order_progress" }
      ]
    };
  },
  mounted() {
    console.log(this.databasePath);
    if (this.databasePath === "/order") {
      console.log("List All");
      store.dispatch(namespaced + "/FETCH_LIST", this.databasePath);
    }
    if (this.databasePath.includes("/order/customer/")) {
      console.log("List " + this.databasePath);
      store.dispatch(namespaced + "/FETCH_FILTERED_LIST", this.databasePath);
      // this.pageList = store.filtered_list;
    }
    // if (this.databasePath.includes("/customer")) {
    //   this.sortBy = "order_datetime";
    //   this.sortDesc = true;
    // }
  },
  methods: {
    ...mapActions(namespaced, ["FETCH_LIST", "FETCH_FILTERED_LIST"])
  },
  computed: {
    ...mapGetters(namespaced, ["list", "filtered_list"])
  }
};
</script>



<!--
	***

	BELOW IS STYLING OF WEBPAGE SCOPED
	
	***
	!-->
<style scoped>
table {
  flex-grow: 1;
}
</style>
