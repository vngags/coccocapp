<template lang="html">
    <div class="follow">       
        <button v-show="loading" type="button" class="btn button-default disabled btn-sm minw80 relative loading">
            <div class="spinner">
                  <div class="bounce1"></div>
                  <div class="bounce2"></div>
                  <div class="bounce3"></div>
            </div>  
        </button>
        <button v-show="!loading" v-if="status == 0" @click="add_follow" type="button" class="btn button-success btn-sm"><i class="iconfont ic-addpeople"></i> Theo dõi</button>
        <ul v-show="!loading" v-if="status == 'following'" class="pl0 no-list-style m0">
            <li class="dropdown">
                <button type="button" class="btn button-default btn-sm" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                    <i class="iconfont ic-follows"></i> Đang theo dõi <i class="iconfont ic-arrow rotate-90"></i>
                </button>
                <ul class="dropdown-menu with-arrow with-arrow-center mt10">
                    <li>
                        <a class="boldi text-capitalize block pointer">
                            <i class="iconfont ic-navigation-notification"></i> Nhận thông báo
                            <small class="normali text-capitalize block text-brown">Thông báo khi có bài viết mới</small>
                       </a>
                    </li>
                    <hr class="mt0 mb0">
                    <li>
                        <a @click="remove_follow" class="boldi text-capitalize block pointer">
                            <i class="iconfont ic-unfollow"></i> Hủy theo dõi
                            <small class="normali text-capitalize block text-brown">Hủy bỏ theo dõi</small>
                       </a>
                    </li>
                </ul>
            </li>
        </ul>        
    </div>
</template>

<script>
import { post, get } from '../../../api'
export default {
    props: ['member_id'],
    data() {
        return {
            loading: true,
            status: null,
        }
    },
    watch: {
        'user': 'check_follow'
    },
    mounted() {
        let vm = this        
        setTimeout(function() {            
            if(vm.user.length == 0) {
                vm.loading = false
                vm.status = 0                
            }            
        }, 500)        
    },
    methods: {
        check_follow() {
            if(this.user.id) {
                post(`/api/v1/check_follow`, {
                    user_id: this.member_id
                }).then(resp => {
                    if(resp.data == 0) {
                        this.status = 0;
                    }
                    if(resp.data == 'following') {
                        this.status = 'following';
                    }
                })
            }else{
                this.status = 0;
            }
            this.loading = false
        },
        add_follow() {
            if(this.user.id) {
                this.loading = true;
                post(`/api/v1/follow`, {
                    user_id: this.member_id
                }).then(resp => {
                    if(resp.data == 1) {
                        this.status = 'following';
                    }
                    this.loading = false;
                })
            }else{
                swal({
                    html: '<div class="pb10i">Vui lòng <strong><a href="/login">đăng nhập</a></strong> để sử dụng chức năng này</div>',
                    showCancelButton: false,
                    showConfirmButton: false,
                 });//end then swal
            }
        },
        remove_follow() {
            this.loading = true
            post(`/api/v1/unfollow`, {
                user_id: this.member_id
            }).then(resp => {
                if(resp.data == 1) {
                    this.status = 0
                }
                this.loading = false
            })
        }
    },//methods
    computed: {
        user() {
           return this.$store.state.auth_user
        },
    }
}
</script>

<style lang="css">
.follow button {
    font-size: 13px;
}
.follow .dropdown-menu a {
    font-size: 13px;
}
.follow .btn.disabled {
    background: rgba(0,0,0,0.1);
    cursor: default;
}
.follow .btn.disabled.loading {
    height: 30px;
}
.follow .btn.disabled.loading .spinner {
    text-align: center;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 36px;
    height: 12px;
    margin: 0;
    z-index: 1;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
}
.follow .btn.disabled.loading .spinner .bounce1 {
    -webkit-animation-delay: -0.32s;
    animation-delay: -0.32s;
}
.follow .btn.disabled.loading .spinner .bounce2 {
    -webkit-animation-delay: -0.16s;
    animation-delay: -0.16s;
}
.follow .btn.disabled.loading .spinner > div {
    width: 12px;
    height: 12px;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 100%;
    display: inline-block;
    -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
    animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        animation-delay: 0s;
    float: left;
}

</style>
