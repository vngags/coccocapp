import Vuex from 'vuex'
import Vue from 'vue'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        auth_user: [],
        comments: [],
        count_comments: 0,
        notifications: [],
        messages_notis: [],
        likes: [],
        user_like: 0,
        chatters: [],
        messages: [],
        chatter: null,
        chatbox_options: {
            show: false,
            dance: false
        },
        message_user_typing: null
    },
    getters: {        
        all_comments(state) {
            return state.comments;
        },
        get_count_comments(state) {
            return state.count_comments
        },
        get_all_notifications(state) {
            return state.notifications
        },
        get_article_likes(state) {
            return state.likes
        },
        get_like_by_user(state) {
            return state.user_like
        },
        get_all_chatter(state) {
            return state.chatters
        },
        get_all_messages(state) {
            return state.messages
        },
        get_chatter(state) {
            return state.chatter
        },
        get_all_messages_notis(state) {
            return state.messages_notis
        },
        get_chatbox_options(state) {
            return state.chatbox_options
        },
        get_message_user_typing(state) {
            return state.message_user_typing
        }
    },
    mutations: {
        message_typing(state, user) {
            state.message_user_typing = user
        },
        remove_message_typing(state, data) {
            state.message_user_typing = null
        },
        set_chatbox_options(state, data) {
            state.chatbox_options = data
        },
        add_new_messages_notis(state, message) {
            var m = state.messages_notis.find((u) => {
                return u.user.sender.code === message.user.sender.code
            });
            var count = 0;
            if(m) {
                //remove old before pus
                var index = state.messages_notis.indexOf(m)
                state.messages_notis.splice(index, 1)
                if(m.count) {
                    count = m.count + 1
                }else{
                    count += 1
                }
            }            
            state.messages_notis.unshift({
                message: message.message,
                user: message.user,
                count: count
            })                      
        },
        select_chatter(state, code) {
            var user = state.chatters.find((u) => {
                return u.code === code
            });
            state.chatter = user
        },
        remove_chatter(state, code) {
            state.chatter = null
        },
        refresh_messages(state, data) {
            state.messages = []
        },

        add_messages(state, data) {
            var user = state.chatters.find((u) => {
                return u.code === data.from
            });
            if(data.from === state.auth_user.code) {
                user = state.auth_user
            }
            var last_item = _.last(state.messages)
            //Check messages length > 0
            if(state.messages.length > 0) {
                if(last_item.user.code === data.from) {
                    //Get last item           
                    _.last(state.messages).data.push({message: data.message, time: data.time})
                }else{
                    state.messages.push({
                        user: user,
                        data: [ {message: data.message, time: data.time} ]
                    })
                }
               
            }else{
                state.messages.push({
                    user: user,
                    data: [ {message: data.message, time: data.time} ]
                })
            }

            // state.messages.push({
            //     user: user,
            //     message: data.message,
            //     time: data.time
            // })            
        },        

        add_chatters(state, user) {
            state.chatters.unshift(user)
        },
        add_chatter_if_not_exists(state, user) {
            var users = state.chatters.find((u) => {
                return u.code === user.code
            });
            if(!users) {
                state.chatters.unshift(user)
            }            
        },
        add_count_comments(state, count) {
            state.count_comments = count
        },
        auth_user_data(state, user) {
            state.auth_user = user
        },
        update_user_avatar(state, avatar) {
            state.auth_user.avatar = avatar
        },
        add_comment(state, comment) {
            state.comments.push(comment);
        },
        add_new_comment(state, comment) {
            state.comments.unshift(comment);
        },
        post_new_replies_lv1(state, comment) {
            var cm = state.comments.find((c) => {
                  return c.id === comment.reply_id
            })
            cm.replies.push(comment)
        },
        add_notification(state, notification) {
            state.notifications.push(notification)
        },
        add_new_notification(state, not) {
            state.notifications.unshift(not)
        },
        add_article_like(state, like) {
            state.likes.push(like)
        },
        check_user_is_liked(state, user_id) {
            var like = state.likes.find((c) => {
                  return c.id === user_id
            })
            if(like) {
                state.user_like = 1
            }
        }
    }
})
