<template lang="html">
    <div class="new_comment">
        <div class="avatar w40 pull-left">
            <a :href="user.slug ? '/u/' + user.slug : 'javascript:void(0)'" class="avatar-circle">
                <img v-if="!user.id" src="/images/default-avatar.png" width="40" class="img-circle">
                <img v-else :src="'/image/26/26/' + user.avatar" class="img-circle" :alt="user.name" :title="user.name">
            </a>
        </div>
        <div class="form ml50">
            <div class="form-comment">
                <textarea name="name" rows="1" cols="80" id="comment_text" class="form-control" placeholder="Viết bình luận" v-model="message"></textarea>
                <p class="clearfix">
                    <button @click="save" type="button" class="btn button-primary pull-right">Gửi bình luận</button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import autosize from 'autosize';
export default {
    props: ['child1_id', 'child2_id'],
    data() {
        return {
            message: null
        }
    },
    computed: {
       user() {
          return this.$store.state.auth_user
       }
    },//end computed
    watch: {
        'message' : 'resize_box'
    },
    methods: {
        resize_box() {
            autosize($('#comment_text'));
        },
        save() {
            if(this.message != null && this.message != ' ' && this.message.replace(/\n/g, "") != '') {
                if(!this.child1_id && !this.child2_id) {
                    this.$emit('onSubmit', {
                        type: 'parent',
                        message: this.message
                    });
                }
                if(this.child1_id) {
                    this.$emit('onSubmit', {
                        type: 'child1',
                        reply_id: this.child1_id,
                        message: this.message
                    });
                }
                this.message = ''
            }
        }
    },//end methods
}
</script>

<style lang="css">

</style>
