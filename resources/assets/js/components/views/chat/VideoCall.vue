<template>
  <div class="video-call">
      <div class="top">
          <span>Đến: <span v-if="chatter && chatter != undefined" class="name"><a :href="'/u/'+chatter.slug">{{ chatter.name }}</a></span></span>
          <ul class="list-inline pull-right">
              <li><a href=""><i class="fa fa-phone size20i"></i></a></li>
              <li><a @click="is_videocall = true"><i class="fa fa-video-camera size20i"></i></a></li>
          </ul>
      </div>
      <div class="video-call-main">
        <video id="myvideo"></video>
        <video id="themvideo"></video>
      </div>
  </div>
</template>

<script>
import { post, get } from "../../../api";
export default {
  computed: {
    user() {
      return this.$store.state.auth_user;
    },
    chatter() {
      return this.$store.getters.get_chatter;
    }
  }
};
</script>

<style lang="scss">
.video-call {
  width: 100%;
  height: calc(100% - 102px);
  & .video-call-main {
    position: relative;
    width: 100%;
    height: 100%;
    & #myvideo {
      width: 100%;
      height: 100%;
      position: relative;
      background: #fff;
    }
    & #themvideo {
      position: absolute;
      background: #fff;
      width: 200px;
      height: 200px;
      bottom: 10px;
      right: 10px;
      border: 1px solid #eee;
    }
  }
}
</style>

