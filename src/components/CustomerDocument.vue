<template>
  <b-container fluid>
    <vue-json-pretty :data="appDocument"></vue-json-pretty>
  </b-container>
</template>


<!--
	***

	VUE scripts

	***
-->
<script>
import { mapActions, mapGetters } from "vuex";
import { store } from "@/store/store";

const namespaced = "customer";

import VueJsonPretty from "vue-json-pretty";

export default {
  components: {
    VueJsonPretty
  },
  data() {
    return {};
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
