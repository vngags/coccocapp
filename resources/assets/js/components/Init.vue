<template lang="html">
   <div></div>
</template>

<script>
import { post, get } from "../api";
export default {
  mounted() {
    //   this.get_auth_user_data()
    this.get_user_data();
  },
  methods: {
    get_user_data() {
      get(`/api/v1/user`).then(resp => {
        this.$store.commit("auth_user_data", resp.data);
      });
    }
  }
};
</script>

<style lang="css">

</style>
