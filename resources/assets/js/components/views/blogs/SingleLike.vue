<template lang="html">
  <div class="single-like">
      <div class="like flexbox">
          <a class="like-button" @click="like" :class="{ 'liked' : likeByUser }">
              <span class="like-icon">
                  <div class="heart-animation-1"></div>
                  <div class="heart-animation-2"></div>
              </span>
              <b>Thích</b>
          </a>
          <span class="like-count">{{ likes.length }}</span>
      </div>
      <audio id="article_like_audio">
         <source src="/audio/comment/all-eyes-on-me.mp3">
         <source src="/audio/comment/all-eyes-on-me.ogg">
         <source src="/audio/comment/all-eyes-on-me.m4r">
     </audio>
  </div>
</template>

<script>
import { get, post } from '../../../api'
export default {
    props: ['post_id'],
    data() {
        return {
            active: false
        }
    },
    mounted() {
        this.fetchLikes()
    },
    methods: {
        fetchLikes() {
            let vm = this
            get(`/api/v1/get_article_likes/${this.post_id}`)
            .then(resp => {
                if(resp.data) {
                    resp.data.forEach(function(like) {
                        vm.$store.commit('add_article_like', like)
                    })
                    if(this.user.id) {
                        this.$store.commit('check_user_is_liked', this.user.id)
                    }
                }
            })
        },
        like() {
            let vm = this
            if(this.user.id) {
                post(`/api/v1/like_article`, {
                    article_id: this.post_id
                }).then(resp => {
                    resp.data.likes.forEach(function(like) {
                        vm.$store.commit('add_article_like', like)
                    })
                    if(this.user.id) {
                        this.$store.commit('check_user_is_liked', this.user.id)
                    }
                })
            }else{
              swal({
                    html: '<div class="pb10i">Vui lòng <strong><a href="/login">đăng nhập</a></strong> để sử dụng chức năng này</div>',
                    showCancelButton: false,
                    showConfirmButton: false,
                 });//end then swal
            }
        },
        checked() {
            this.active = !this.active
            document.getElementById("article_like_audio").play()
        }
    },//end methods
    computed: {
        user() {
            return this.$store.state.auth_user
        },
        likes() {
            return this.$store.getters.get_article_likes
        },
        likeByUser() {
            return this.$store.getters.get_like_by_user
        }
    },
}
</script>

<style lang="css">
</style>
