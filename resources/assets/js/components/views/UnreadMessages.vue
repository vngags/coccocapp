<template lang="html">
    <li class="messages dropdown">
        <a class="message" :data-count="message_notis.length" :title="[message_notis.length > 0 ? message_notis.length : 'Không có'] + ' tin nhắn mới'" :class="{ 'show-count' : message_notis.length > 0 }" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"></a>
        <ul class="dropdown-menu with-arrow with-arrow-center">
            <div class="notify-title">
                <h4>Tin nhắn</h4>
            </div>
            <div class="scroller" :style="{ 'max-height': winHeight + 'px' }">
                <li v-if="message_notis.length == 0">
                    <p class="text-center text-brown pt20 pb20 mb0">Không có tin nhắn mới</p>
                </li>

                <li v-for="not in message_notis">
                    <!-- <a :href="'/chat/'+not.user.sender.code"> -->
                    <a @click="openChatBox">
                        <div class="w55 pull-left">
                            <a :href="'/u/'+not.user.sender.slug" class="avatar-circle">
                                <img :src="'/image/48/48/'+not.user.sender.avatar" class="img-cicle">
                            </a>
                        </div>
                        <div class="ml55">
                            <p class="bold m0">
                                <a :href="'/u/'+not.user.sender.slug">{{ not.user.sender.name }}</a>
                                <span v-if="not.count > 0" class="count">({{ not.count }})</span>
                            </p>
                            <span v-html="not.message"></span>
                            <p><small><timeago :since="not.created_at ? not.created_at : new Date()" :auto-update="60"></timeago></small></p>
                        </div>
                    </a>
                </li>

            </div>
            <div class="notify-footer">
                <a href="/chat" class="text-center block size13" target="_blank">Xem tất cả</a>
            </div>
        </ul>
        <audio id="new_message">
             <source src="/audio/messages/notification.mp3">
        </audio>
        <audio id="new_message_notis">
             <source src="/audio/messages/notification48.mp3">
        </audio>
   </li>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      winHeight: 0,
      chatting: null
    };
  },
  mounted() {
    let vm = this;
    this.listen();
    this.getWinSize();
    this.$nextTick(function() {
      window.addEventListener("resize", vm.getWinSize);
    });
  },
  methods: {
    listen() {
      console.log("listening");
      //Listen for message
      Echo.private("App.User." + this.id)
        .listen("MessagePosted", e => {
          this.chatting = e.user.sender;
          this.$store.commit("remove_message_typing", 0);
          if (this.chatter && this.chatter != null) {
            if (e.user.sender.id === this.chatter.id) {
              this.$store.commit("add_messages", {
                message: e.message,
                from: e.user.sender.code
              });
              document.getElementById("new_message").play();
              //Hieu ung nhap nhay
              if (this.options.show == false) {
                this.$store.commit("set_chatbox_options", {
                  show: false,
                  dance: true
                });
                this.$store.commit("add_new_messages_notis", {
                  message: e.message,
                  user: e.user
                });
              }
            }
            //else send to notis
          } else {
            //sound
            this.$store.commit("add_new_messages_notis", {
              message: e.message,
              user: e.user
            });

            document.getElementById("new_message_notis").play();
          }
        })
        .listen("MessageTyping", e => {
          this.$store.commit("message_typing", e.user.sender);
        });
    },
    openChatBox() {
      this.$store.commit("set_chatbox_options", {
        show: true
      });
      this.$store.commit("add_chatter_if_not_exists", this.chatting);
      this.$store.commit("select_chatter", this.chatting.code);
    },
    getWinSize() {
      this.winHeight =
        document.documentElement.clientHeight - 180 >= 100
          ? document.documentElement.clientHeight - 180
          : 100;
    }
  }, //methods
  beforeDestroy() {
    window.removeEventListener("resize", this.getWinSize);
  },
  computed: {
    chatter() {
      return this.$store.getters.get_chatter;
    },
    message_notis() {
      return this.$store.getters.get_all_messages_notis;
    },
    options() {
      return this.$store.getters.get_chatbox_options;
    }
  }
};
</script>

<style lang="css">

</style>
