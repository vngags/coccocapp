<template lang="html">
    <div class="row">
        <!-- Left Column -->
        <div class="left wHeight">
            <div class="top">
                <input type="text" placeholder="Tìm kiếm theo tên"/>
            </div>
            <ul class="people scroller">
                <transition-group name="fade">
                    <li v-for="member in chatters" :key="member.id" @click="select(member.code)" :class="activeClass(member.code)" class="person" :data-chat="member.code">                        
                        <img :src="'/image/48/48/' + member.avatar" />
                        <div>                            
                            <span class="name">{{ member.name }}</span>
                            <span class="time">1:44 PM</span>
                            <span class="preview">I've forgotten how it felt before</span>
                        </div>                        
                    </li>
                </transition-group>
            </ul>
        </div>
        <!-- \ Left Column -->
        <!-- Right Column -->
        <div class="right">
            <div class="r_row">
                <div class="_left wHeight">
                    <div class="top">
                        <span class="text-center bold block">Thông tin thành viên</span>
                    </div>
                    <div v-if="chatter && chatter != undefined" class="profile-info scroller">
                        <div class="profile-cover-panel">
                            <transition name="fade">
                                <div class="profile-cover" :class="{ active : chatter.avatar }" :style="chatter.avatar ? 'background:url(/image/96/96/'+chatter.avatar+');' : 'http://placehold.it/96x96'"></div>
                            </transition>
                        </div>

                        <div class="avatar-circle avatar-outline">
                            <img :src="'/image/96/96/' + chatter.avatar" class="img-circle">
                        </div>
                        <div class="pt20"></div>
                        <div class="profile-detail">
                            <h3 class="text-center">
                                <a :href="'/u/'+ chatter.slug">{{ chatter.name }}</a>
                            </h3>
                        </div>
                        <div class="profile-follow">
                            <!-- FOLLOWING -->
                            <div class="following with-border-bottom">
                                <h4 class="size13 text-uppercase bold"><i class="iconfont ic-followed"></i> Đang theo dõi <span class="badge">{{ chatter.followings.length }}</span></h4>
                                <ul class="list-inline text-center">
                                    <li v-for="following in chatter.followings" :data-tooltip="following.name" data-placement="top">
                                        <a :href="'/u/'+following.slug" class="avatar-circle avatar-outline">
                                            <img :src="'/image/32/32/'+following.avatar" class="img-circle">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- FOLLOWING -->

                            <!-- FOLLOWER -->
                            <div class="followers with-border-bottom">
                                <h4 class="size13 text-uppercase bold"><i class="iconfont ic-follows"></i> Người Theo dõi <span class="badge">{{ chatter.followers.length }}</span></h4>
                                <ul class="list-inline text-center">
                                    <li v-for="follower in chatter.followers" :data-tooltip="follower.name" data-placement="top">
                                        <a :href="'user.index'+follower.slug" class="avatar-circle avatar-outline">
                                            <img :src="'/image/32/32/'+follower.avatar" class="img-circle">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- FOLLOWER -->
                        </div> 
                    </div>
                </div>
                <div class="_right wHeight">                    
                    <div class="top">
                        <span>Đến: <span v-if="chatter && chatter != undefined" class="name"><a :href="'/u/'+chatter.slug">{{ chatter.name }}</a></span></span>
                        <ul class="list-inline pull-right">
                            <li><a href=""><i class="fa fa-phone size20i"></i></a></li>
                            <li><a href=""><i class="fa fa-video-camera size20i"></i></a></li>
                        </ul>
                    </div>
                    <!-- Loading -->
                    <transition name="fade">
                        <div v-if="loading" class="spinner">
                              <div class="bounce1"></div>
                              <div class="bounce2"></div>
                              <div class="bounce3"></div>
                        </div>  
                    </transition>
                    <!-- \Loading -->
                    <!-- Chat Content -->
                    <div v-show="!loading" class="chat clearfix relative scroller" id="chat-main" :data-chat="chatter" v-chat-scroll>
                        <!-- <transition name="fade"> -->
                            <div v-if="messages.length == 0" class="text-center text-brown no-content">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="200px" height="200px" viewBox="0 0 16 16">
                                <path fill="#d8e3ec" d="M8 1c3.9 0 7 3.1 7 7s-3.1 7-7 7-7-3.1-7-7 3.1-7 7-7zM8 0c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8v0z"/>
                                <path fill="#d8e3ec" d="M7 6c0 0.552-0.448 1-1 1s-1-0.448-1-1c0-0.552 0.448-1 1-1s1 0.448 1 1z"/>
                                <path fill="#d8e3ec" d="M11 6c0 0.552-0.448 1-1 1s-1-0.448-1-1c0-0.552 0.448-1 1-1s1 0.448 1 1z"/>
                                <path fill="#d8e3ec" d="M11.3 12.3c-0.7-1.1-2-1.8-3.3-1.8s-2.6 0.7-3.3 1.8l-0.8-0.6c0.9-1.4 2.4-2.2 4.1-2.2s3.2 0.8 4.1 2.2l-0.8 0.6z"/>
                                </svg>
                            </div>
                        <!-- </transition> -->
                        <div v-for="data in messages" :class="bubbleClass(data)" class="bubble">
                            <!-- <div class="conversation-start">
                                <span>Today, 5:38 PM</span>
                            </div> -->
                            <div class="chat-content-group clearfix">

                                <span v-for="content in data.data" class="bubble-row">
                                    <div class="chat-content">
                                        {{ content.message }}

                                        <div v-if="data.user && data.user.code != user.code" class="avatar">
                                            <a :href="'/u/' + data.user.slug" class="avatar-circle">
                                                <img :src="'/image/32/32/' + data.user.avatar" class="img-circle">
                                            </a> 
                                        </div>

                                        <p><small><timeago :since="content.time ? content.time : new Date()" :auto-update="60"></timeago></small></p>
                                    </div>                                    
                                </span>  

                            </div>                                                      
                        </div>
                        <div v-if="user_typing" class="bubble you typing">
                             {{ user_typing.name }} đang viết...
                        </div>

                    </div>
                    <!-- \Chat Content -->
                    <!-- Write -->
                    <div class="write">
                        <a href="javascript:;" class="write-link attach"></a>
                        <input @keyup.enter="send_message" @keypress="typing_message" autofocus="true" type="text" placeholder="Viết tin nhắn..." v-model="message" />
                        <a href="javascript:;" class="write-link smiley"></a>
                        <a @click="send_message" href="javascript:;" class="write-link send"></a>
                    </div>
                    <!-- \Write -->                    
                </div>
            </div>
        </div>     
        <!-- \ Right Column -->   
    </div>
</template>

<script>
import { post, get } from '../../../api'
export default {
    props: ['chatting'],
    data() {
        return {
            loading: false,
            message: null,
            winHeight: 0,
            fetched: false,
            is_typing: false
        }
    },
    mounted() {
        let vm = this
        this.fetchMembers();
        this.getWinSize();
        this.$nextTick(function() {
              window.addEventListener('resize', vm.getWinSize);
        })
    },
    methods: {
        activeClass(code) {
            if(this.chatter && this.chatter.code != undefined) {
                return (this.chatter.code == code) ? 'active' : ''
            }
        },
        fetchMembers() {
            let vm = this
            get('/api/v1/message/get_users')
            .then(resp => {
                this.$store.commit('refresh_messages', 0)
                resp.data.forEach(function(item) {
                    vm.$store.commit('add_chatters', item)
                })
            })
        },
        selectChatter() {
            if(this.chatting) {
                post(`/api/v1/message/get_user/${this.chatting}`)
                .then(resp => {
                    //push to chatters if not exists
                    this.$store.commit('add_chatter_if_not_exists', resp.data)
                    this.select(this.chatting)
                })                
            }            
        },
        select(code) {
            let vm = this
            

            if(this.fetched == false) {
                $('#chat-main').removeClass('active-chat');
                $('._left .profile-info').removeClass('active')
                this.fetchSelectData(code);
            }else{
                if(this.chatter.code != code) {
                    $('#chat-main').removeClass('active-chat');
                    $('._left .profile-info').removeClass('active')
                    this.fetchSelectData(code);
                }
            }        
            setTimeout(function() {
                $('#chat-main').addClass('active-chat');
                $('._left .profile-info').addClass('active')
            },10)
        },
        fetchSelectData(code) {
            let vm = this
            this.loading = true
            this.$store.commit('select_chatter', code)
            get(`/api/v1/message/${code}`)
            .then(resp => {
                this.$store.commit('refresh_messages', 0)
                if(resp.data.status == 'avalible') {
                    console.log('No History');
                }else{                    
                    resp.data.forEach(function(item) {
                        vm.$store.commit('add_messages', item)
                        //Scroll to bottom
                        vm.$nextTick(function() {
                            var container = document.getElementById("chat-main");
                            container.scrollTop = container.scrollHeight;
                        }) 
                    })
                    this.fetched = true      
                }
                this.loading = false
                window.history.pushState("object or string", "Title", "/chat/"+code);                
            })
        },
        send_message() {
            if(this.message && this.chatter) {
                post(`/api/v1/message/${this.chatter.code}`, {
                    message: this.message
                }).then(resp => {
                    if(resp.data) {
                        this.$store.commit('add_messages', {
                            from: this.user.code,
                            message: this.message,
                            time: new Date()
                        })
                    }
                    this.message = ''
                })
            }
        },
        typing_message() {
            if(this.message && this.message.length >= 3 && this.is_typing == false) {
                post(`/api/v1/message/typing`, {
                    user_code: this.chatter.code
                }).then(resp => {
                    this.is_typing = true
                })
            }else{
                this.is_typing = false
            }            
        },
        bubbleClass(data) {
            if(data.user != 'undefined' && data.user.code != this.user.code) {
                return 'you';
            }else{
                return 'me'
            }
        },
        getWinSize() {
            this.winHeight = document.documentElement.clientHeight - 60;
        }
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.getWinSize);
    },
    computed: {
        user() {
           return this.$store.state.auth_user
        },
        chatters() {
           return this.$store.getters.get_all_chatter
        },
        messages() {
            return this.$store.getters.get_all_messages
        },
        chatter() {
            return this.$store.getters.get_chatter
        },
        user_typing() {
            return this.$store.getters.get_message_user_typing
        }
    },
    watch: {
        chatters() {
            this.selectChatter()
        },
        user_typing() {
            this.$nextTick(function() {
                var container = document.getElementById("chat-main");
                container.scrollTop = container.scrollHeight;
            }) 
        }
    },
}
</script>

<style lang="scss">
.typing {
    color: #677778 !important;
}
</style>
