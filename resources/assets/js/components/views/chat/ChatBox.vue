<template>
	<div class="chatbox chat">
		<div v-if="chatter" class="chatbox-content" :class="{ dance : dance_action }">
			<header class="chatbox-header" :class="{ active : options.show }">
				<h4 @click="toggleOptionShow" class="chatbox-title">
					<a :href="options.show ? '/u/' + chatter.slug : 'javascript:void(0)'">{{ chatter.name }}</a>
					<span @click="closeChat">&times;</span>
				</h4>				
			</header>
			<div v-show="options.show" class="chatbox-body scroller" id="chat-main" v-chat-scroll>

				<div v-for="data in messages" :class="bubbleClass(data)" class="bubble">
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

			</div>
			<!-- Write -->
            <div v-if="options.show" class="write">
                <a href="javascript:;" class="write-link attach"></a>
                <input @keyup.enter="send_message" autofocus="true" type="text" placeholder="Viết tin nhắn..." v-model="message" />
                <a href="javascript:;" class="write-link smiley"></a>
                <a @click="send_message" href="javascript:;" class="write-link send"></a>
            </div>
            <!-- \Write -->    
		</div>
	</div>
</template>
<script>
import { post, get } from '../../../api'
export default {
	data() {
		return {
			action: false,
			loading: false,
			message: null,
			dance_action: false
		}	
	},
	mounted() {
		let vm = this				
	},
	watch: {
		'chatter.code' : 'fetchSelectData',
		'options.dance' : 'dance'
	},
	methods: {
		dance() {
			let vm = this
			var interval = null
			if(this.options.dance) {
				this.dance_action = true
			}else{
				this.dance_action = false
			}
		},
		toggleOptionShow() {
			this.$store.commit('set_chatbox_options', {
				show: !this.options.show,
				dance: false
			})
            this.$nextTick(function() {
                var container = document.getElementById("chat-main");
                container.scrollTop = container.scrollHeight;
            }) 
		},
		fetchSelectData() {
            let vm = this
            this.loading = true
            get(`/api/v1/message/${this.chatter.code}`)
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
                }
                this.loading = false     
            })
        },
        bubbleClass(data) {
            if(data.user != 'undefined' && data.user.code != this.user.code) {
                return 'you';
            }else{
                return 'me'
            }
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
        closeChat() {
        	this.$store.commit('remove_chatter', 0)
        }
	},//end methods
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
        options() {
        	return this.$store.getters.get_chatbox_options
        }
    },
}
</script>
