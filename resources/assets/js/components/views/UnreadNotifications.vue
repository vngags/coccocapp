<template lang="html">
    <li class="notifications dropdown">
        <a class="notification" :data-count="notify.length" :title="[notify.length > 0 ? notify.length : 'Không có'] + ' thông báo mới'" :class="{ 'show-count' : notify.length > 0 }" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"></a>
        <ul class="dropdown-menu with-arrow with-arrow-center">
            <div class="notify-title">
                <h4>Thông báo</h4>
            </div>
            <div class="scroller" :style="{ 'max-height': winHeight + 'px' }">
                <li v-if="notify.length == 0">
                    <p class="text-center text-brown pt20 pb20 mb0">Không có thông báo mới</p>
                </li>
                <li v-for="not in notify">
                    <a :href="not.data.url">
                        <div class="w55 pull-left">
                            <a :href="'/u/'+not.data.user.slug" class="avatar-circle">
                                <img :src="'/image/48/48/'+not.data.user.avatar" class="img-cicle">
                            </a>
                        </div>
                        <div class="ml55">
                            <a :href="'/u/'+not.data.user.slug"><strong>{{ not.data.user.name }}</strong></a> <span v-html="not.data.message"></span>
                            <p><small><timeago :since="not.created_at ? not.created_at : new Date()" :auto-update="60"></timeago></small></p>
                        </div>
                    </a>
                </li>
            </div>
            <div class="notify-footer">
                <a href="/notifications" class="text-center block size13" target="_blank">Xem tất cả</a>
            </div>
        </ul>
        <audio id="bell_audio">
            <source src="/audio/ring_bell/chime.mp3">
            <source src="/audio/ring_bell/chime.ogg">
            <source src="/audio/ring_bell/chime.m4r">
       </audio>
   </li>
</template>

<script>
import { post, get } from "../../api";
// import timeago from '../../../../../public/plugins/timeago/jquery.timeago.js'
export default {
  props: ["id"],
  data() {
    return {
      winWidth: 0,
      winHeight: 0
    };
  },
  mounted() {
    let vm = this;
    this.get_unread();
    this.listen();
    this.listen_article_posted();
    this.getWinSize();
    this.$nextTick(function() {
      window.addEventListener("resize", vm.getWinSize);
    });
  },
  methods: {
    listen() {
      Echo.private("App.User." + this.id).notification(notification => {
        $(".notification").removeClass("notify");
        setTimeout(function() {
          $(".notification").addClass("notify");
        }, 10);
        document.getElementById("bell_audio").play();
        this.$store.commit("add_new_notification", {
          data: notification
        });
      });
    },
    listen_article_posted() {
      //Join vào phòng chúng ta đã đăng kí ở trên là chatroom.1
      Echo.private("App.User." + this.id).listen("ArticlePostedHandler", e => {
        console.log(e);

        $(".notification").removeClass("notify");
        setTimeout(function() {
          $(".notification").addClass("notify");
        }, 10);
        document.getElementById("bell_audio").play();
        this.$store.commit("add_new_notification", {
          data: {
            message: `vừa đăng một bài viết mới: <strong><a href="/${e.post
              .slug}-${e.post.id}.html">${e.post.title}</a></strong>`,
            user: e.author
          }
        });
      });
    },
    ready: function() {
      $("small#timeago").timeago();
    },
    get_unread() {
      let vm = this;
      get(`/api/v1/get_unread`).then(resp => {
        resp.data.forEach(function(item) {
          vm.$store.commit("add_notification", item);
        });
      });
    },
    getWinSize() {
      this.winWidth = document.documentElement.clientWidth;
      this.winHeight =
        document.documentElement.clientHeight - 180 >= 100
          ? document.documentElement.clientHeight - 180
          : 100;
    }
  }, //methods
  computed: {
    notify() {
      return this.$store.getters.get_all_notifications;
    }
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.getWinSize);
  }
};
</script>

<style lang="css">

</style>
