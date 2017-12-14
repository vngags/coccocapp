<template lang="html">
    <div>
        <audio id="bell_audio">
            <source src="/audio/ring_bell/chime.mp3">
            <source src="/audio/ring_bell/chime.ogg">
            <source src="/audio/ring_bell/chime.m4r">
       </audio>
    </div>
</template>

<script>
export default {
  mounted() {
    console.log("mounted");
    this.listen();
  },
  methods: {
    listen() {
      Echo.private("App.User." + this.id).notification(notification => {
        $(".notification").removeClass("notify");
        setTimeout(function() {
          $(".notification").addClass("notify");
        }, 10);
        document.getElementById("bell_audio").play();
        this.$store.commit("add_notification", notification);
      });
    }
  } //methods
};
</script>

<style lang="css">

</style>
