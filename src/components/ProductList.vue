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
      borderless
      small
      dark
      footClone
      show-empty
      v-bind:fields="fields"
      v-bind:items="list"
      v-bind:sort-by.sync="sortBy"
      v-bind:sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :filterIncludedFields="filterOn"
    >
      <!-- A virtual column -->
      <template v-slot:cell(name)="data">
        <span>
          <a
            :href="`/product/${data.item.ptn}`"
          >{{data.item.product_code }}-{{ data.item.product_number }} {{ data.item.product_spec }}</a>
        </span>
        <span class="right">{{ data.item.ext_ref}}</span>
      </template>

      <template v-slot:cell(index)="data">{{ data.index+1 }}</template>
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

const namespaced = "product";

export default {
  data() {
    return {
      filter: null,
      filterOn: [
        "name",
        "product_code",
        "product_number",
        "ext_ref",
        "product_name"
      ],
      totalRows: 1,
      currentPage: 1,
      perPage: 100,
      pageOptions: [100, 500, 2000],
      sortBy: "",
      sortDesc: true,
      // dataList:"",
      // dataListLength:"",

      databasePath: this.$route.path,
      fields: [
        { key: "index", label: "x" },
        { key: "name", label: "รหัสสินค้า" },
        // { key: "product_code", label: "รหัสสินค้า" },
        // {
        //   key: "product_number",
        //   label: "Number",
        //   sortable: true,
        //   variant: "danger",
        //   isRowHeader: true
        // },
        // { key: "ext_ref" },
        { key: "product_name", variant : "info" },
        { key: "colors" },
        { key: "paper_code" },
        { key: "stock" },
        { key: "cell" },
        { key: "print_type" }
      ],
      fieldsThai: [
        "รหัสสินค้า",
        "2",
        "รายการสินค้า",
        "4",
        "ชนิดกระดาษ",
        "สินค้าคงเหลือ",
        "ตระกร้า",
        "รูปแบบการพิมพ์"
      ]
    };
  },
  mounted() {
    console.log(this.databasePath);
    if (this.databasePath === "/product") {
      console.log("List All");
      store.dispatch(namespaced + "/FETCH_LIST", this.databasePath);
    }
    if (this.databasePath.includes("/product/customer/")) {
      console.log("List " + this.databasePath);
      store.dispatch(namespaced + "/FETCH_FILTERED_LIST", this.databasePath);
    }
    if (this.databasePath.includes("/customer")) {
      this.sortBy = "product_number";
      this.sortDesc = false;
    }
  },
  methods: {
    ...mapActions(namespaced, ["FETCH_LIST"])
  },
  computed: {
    ...mapGetters(namespaced, ["list"])
  }
};
</script>



<!--
	***

	BELOW IS STYLING OF WEBPAGE SCOPED
	
	***
	!-->
	<style scoped>
span.right {
  float: right;
}
</style>
