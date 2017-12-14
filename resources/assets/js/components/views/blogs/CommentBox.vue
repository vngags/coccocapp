<template lang="html">
  <div class="comments pt30 pb30">
      <div v-if="!is_fetched" class="comments-placeholder" style="/*! display: none; */">
          <div class="author">
              <div class="avatar"></div>
              <div class="info">
                  <div class="name"></div>
                  <div class="meta"></div>
              </div>
          </div>
          <div class="text"></div>
          <div class="text animation-delay"></div>
          <div class="tool-group">
              <i class="iconfont ic-article-like"></i>
              <div class="zan"></div>
              <i class="iconfont ic-list-comments"></i>
              <div class="zan"></div>
          </div>
      </div>
      <div v-else>
          <div v-if="!user.id" class="singin text-center">
              <p class="size12 mt10 text-brown">Vui lòng <a href="/login" class="btn button-primary btn-xs"><i class="iconfont ic-user"></i> Đăng nhập</a> để gửi bình luận</p>
          </div>
          <div v-else>
              <transition name="fade">
                  <!-- <comment-form @onSubmit="on_submit"></comment-form> -->
                  <comment-form-editor @onSubmit="on_submit"></comment-form-editor>
             </transition>
          </div>
          <div class="comments_list mt30">
              <div v-if="comments.length > 0" class="comments_list_statistic with-border-bottom clearfix">
                  <h4 class="pull-left mb0i bold"><i class="fa fa-comments-o"></i> {{ count_comments }} bình luận</h4>
                  <h4 class="pull-right mb0i">
                      <ul class="list-inline-menu mb0i">
                          <li class="active mr10"><a class="normali pr5i pl5i pointer">Mới nhất</a></li>
                          <li><a class="normali pr5i pl5i pointer">Nổi bật</a></li>
                      </ul>
                  </h4>
              </div>
              <div v-if="comments.length == 0" class="no-comment text-center text-brown p30">
                  Hãy là người đầu tiên bình luận cho bài viết này!
              </div>
              <!-- <div class="comments_list_content"> -->
              <transition-group name="fade" tag="div" class="comments_list_content">
                  <!-- ITEM -->
                      <div v-for="(comment, index) in comments" :key="comment.id" v-if="comments.length > 0" class="comment_item">
                          <div class="comment_author clearfix">
                              <div class="avatar pull-left">
                                  <a :href="'/u/'+comment.user.slug" class="avatar-circle">
                                      <img :src="'/image/40/40/' + comment.user.avatar" class="img-circle">
                                  </a>
                              </div>
                              <div class="meta ml50 clearfix">
                                  <div class="pull-left">
                                      <div>
                                          <a class="name" :href="'/u/'+comment.user.slug">{{ comment.user.name }}</a>
                                          <div class="author_rank inline-block">
                                              <ul class="list-inline">
                                                  <li v-if="comment.user.id == author_id" class="relative" data-tooltip="Tác giả" data-placement="top">
                                                      <img src="/images/author.png" width="14px">
                                                  </li>
                                              </ul>
                                          </div>
                                      </div>
                                      <p class="time">{{ comment.created_at }}</p>
                                  </div>
                                 <a v-if="user.id && comment.user.id !== user.id" class="pull-right size12 mt5 success-color follow-link follow-link-xs" href="#"><i class="iconfont ic-addpeople"></i> Theo dõi</a>
                              </div>
                          </div>
                          <div class="comment_body">
                              <p v-html="comment.comment"></p>
                              <ul class="list-inline ml0 comment_meta text-brown">
                                  <li>
                                      <a><i class="iconfont ic-like"></i> 337 Thích</a>
                                  </li>
                                  <li>
                                      <a @click="openF1Box(index, comment.id)"><i class="iconfont ic-comment"></i> {{ comment.replies.length > 0 ? comment.replies.length : '' }} Trả lời</a>
                                  </li>
                              </ul>
                          </div>
                          <!-- LV1 -->
                           <transition-group name="fade" tag="div" class="level1">
                               <!-- ITEM LEVEL1 -->
                               <div v-for="(level1, index1) in comment.replies" :key="level1.id" v-if="comment.replies.length > 0" class="comment_item clearfix">
                                   <span class="left-line"></span>
                                   <div class="comment_author clearfix">
                                       <div class="avatar pull-left">
                                           <a :href="'/u/'+level1.user.slug" class="avatar-circle">
                                               <img :src="'/image/26/26/' + level1.user.avatar" class="img-circle">
                                           </a>
                                       </div>
                                   </div>
                                   <div class="comment_body">
                                       <div class="meta">
                                           <a class="name size14 bold" :href="'/u/'+level1.user.slug">{{ level1.user.name }}</a>
                                           <small class="text-brown">{{ level1.created_at }}</small>
                                           <div class="author_rank inline-block">
                                               <ul class="list-inline">
                                                   <li v-if="level1.user.id == author_id" class="relative" data-tooltip="Tác giả" data-placement="top">
                                                       <img src="/images/author.png" width="14px">
                                                   </li>
                                               </ul>
                                           </div>
                                       </div>

                                       <p v-html="level1.comment"></p>
                                       <ul class="list-inline mt0 mb0 comment_meta text-brown">
                                           <li>
                                               <a><i class="iconfont ic-like"></i> 337 Thích</a>
                                           </li>
                                       </ul>
                                   </div>
                               </div>
                               <!-- ITEM LEVEL1 -->
                            </transition-group>
                          <!-- LV1 -->
                          <p v-if="comment.replies.length > 0 && !F1Box[index]" class="clearfix">
                              <a @click="openF1Box(index, comment.id)"><i class="fa fa-mail-reply"></i> Trả lời</a>
                          </p>
                          <!-- F1Box -->
                             <transition name="fade">
                                 <comment-form v-if="F1Box[index]" :id="'comment-form-'+comment.id" class="level1 bl-transparent" @onSubmit="on_submit" :child1_id="comment.id"></comment-form>
                             </transition>
                          <!-- F1Box -->
                      </div>
                  <!-- ITEM -->
              </transition-group>
              <!-- </div> -->
              <!-- paginate -->
              <div v-if="page.last_page > 1" class="page_nav clearfix">
                 <div class="comment_page">
                    <button v-if="page.current_page < page.last_page" type="button" :disabled="is_fetching" @click="comment_next_page" class="btn button-primary btn-block">
                        <span v-if="is_fetching"><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
                        <span v-else>Tải thêm {{ (page.total - (page.current_page*page.per_page)) > 10 ? 10 : page.total - (page.current_page*page.per_page) }} bình luận</span>
                    </button>
                 </div>
              </div>
              <!-- paginate -->
          </div>
      </div>
  </div>
</template>

<script>
import CommentForm from './CommentForm.vue'
import CommentFormEditor from './CommentFormEditor.vue'
import { post, get } from '../../../api'
export default {
    props: ['post_id'],
    components: {
        CommentForm,
        CommentFormEditor
    },
    data() {
        return {
            page: {
                current_page: null,
                total: null,
                last_page: null,
                per_page: null
            },
            author_id: null,
            is_fetching: false,
            F1Box: [],
            is_fetched: false
        }
    },
    mounted() {
        // this.fetchComments()
    },
    watch: {
        'is_fetched': 'fetchComments'
    },
    methods: {
        fetchComments() {
            get(`/api/v1/comments/${this.post_id}/?page=1`)
            .then(resp => {
                resp.data.data.forEach((comment) => {
                    this.$store.commit('add_comment', comment);
                })
                this.author_id = resp.data.author_id
                this.page.current_page = resp.data.current_page
                this.page.total = resp.data.total
                this.page.last_page = resp.data.last_page
                this.page.per_page = resp.data.per_page
                this.$store.commit('add_count_comments', resp.data.data.length);
            })
        },
        on_submit(data) {
            if(data) {
                var type = data['type'];
                switch (type) {
                    case 'parent':
                        post('/api/v1/comments', {
                            page_id: this.post_id,
                            comment: data['message'],
                            user_id: this.user.id
                        }).then(resp => {
                            if(resp.data.status) {
                                this.$store.commit('add_new_comment', resp.data.new_comment[0]);
                                this.$store.commit('add_count_comments', (this.count_comments + 1));
                            }
                        })
                        break;
                    case 'child1':
                        post(`/api/v1/comments`, {
                           comment: data['message'],
                           user_id: this.user.id,
                           reply_id: data['reply_id']
                        }).then(resp => {
                           if(resp.data.status) {
                              this.$store.commit('post_new_replies_lv1', resp.data.new_comment[0])
                              this.$store.commit('add_count_comments', (this.count_comments + 1));
                           }
                        })
                        break;
                }//switch
            }
        },
        comment_next_page() {
            let vm = this
            if(this.page.current_page < this.page.last_page) {
               this.is_fetching = true;
               get(`/api/v1/comments/${this.post_id}?page=${this.page.current_page+1}`)
               .then(resp => {
                  resp.data.data.forEach(function(item) {
                     vm.$store.commit('add_comment', item)
                  })
                  this.is_fetching = false;
                  this.page.current_page = resp.data.current_page
                  this.page.total = resp.data.total
                  this.page.last_page = resp.data.last_page

               });
            }
        },
        openF1Box(index, id) {
            if(this.user.id) {
                if(this.F1Box[index]) {
                    this.F1Box = []
                }else{
                    this.F1Box = []
                    Vue.set(this.F1Box, index, 1)
                    setTimeout(function() {
                        $('html, body').animate({ scrollTop: $('#comment-form-'+id).offset().top - $(window).height()/2 }, 'slow');
                        $('#comment-form-'+id+' textarea').focus();
                    },200)
                }
            }
        },
        handleScroll() {
            if(window.scrollY >= $('.comment-box').offset().top - $(window).height()) {
                this.is_fetched = true
            }
        },
    },//end methods
    computed: {
       comments() {
          return this.$store.getters.all_comments
       },
       user() {
          return this.$store.state.auth_user
      },
      count_comments() {
          return this.$store.getters.get_count_comments
      }
    },//end computed
    created () {
      window.addEventListener('scroll', this.handleScroll);
    },
    destroyed () {
      window.removeEventListener('scroll', this.handleScroll);
    }
}
</script>

<style lang="css">
.comments {
    border-top: 2px solid #f1f1f1;
    margin-top: 20px;
}
.comments_list_statistic > .pull-left {
    line-height: 40px;
}

.level1 .list-inline.comment_meta > li > a {
    font-size: 12px;
}
.list-inline.comment_meta > li > a {
    color: #888;
    font-size: 13px;
    cursor: pointer;
}
.list-inline.comment_meta > li > a > i {
    font-size: 14px;
}
.list-inline.comment_meta > li > a:hover {
    color: #e56f5b;
}
.bl-transparent.level1 {
    border-left-color: transparent !important;
}
.author_rank {
    max-height: 23px;
}
.comment_body > p {
    white-space: pre-wrap;
    margin-bottom: 0;
}
.comments_list_content > .comment_item > p > a {
    float: right;
    cursor: pointer;
    color: #999;
    font-size: 13px;
}
</style>
