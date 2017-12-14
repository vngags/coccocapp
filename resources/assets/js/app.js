
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Progress from '../../../public/plugins/progress-bar/progress.js'
require('../../../public/plugins/progress-bar/progress.css')



import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)



import VueTimeago from 'vue-timeago'
Vue.use(VueTimeago, {
  name: 'timeago', // component name, `timeago` by default
  locale: 'en-US',
  locales: {
    // you will need json-loader in webpack 1
    'en-US': require('vue-timeago/locales/vi-VN.json')
  }
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */



// import router from './router'
// import store from './store'
//
//
// import  App from './App'
// Vue.component('app', require('./App.vue'));
Vue.component('init', require('./components/Init.vue'));
Vue.component('unread-messages', require('./components/views/UnreadMessages.vue'));
Vue.component('unread-notification', require('./components/views/UnreadNotifications.vue'));
Vue.component('article-quick-view', require('./components/views/ArticleQuickView.vue'));
Vue.component('chatbox', require('./components/views/chat/ChatBox.vue'));
Vue.component('blog-form', require('./components/views/blogs/Form.vue'));
Vue.component('modal-media', require('./components/views/ModalMedia.vue'));
Vue.component('comment-box', require('./components/views/blogs/CommentBox.vue'));
Vue.component('detail-meta-button', require('./components/views/blogs/DetailMetaButton.vue'));
Vue.component('single-like', require('./components/views/blogs/SingleLike.vue'));
Vue.component('follow', require('./components/views/blogs/Follow.vue'));

//Chat
Vue.component('chat', require('./components/views/chat/Form.vue'));

import {store} from './store'

const app = new Vue({
    el: '#app',
    store
});

// YouTube like progress-bar
Progress.configure({ showSpinner: false });
Progress.start();
$(window).on('load', function () {
   Progress.done(true);
});
// YouTube like progress-bar

$('#toggle-box-checkbox').on('change', function(){
  if(this.checked){
    $('body').addClass('night-mode');
  }else{
    $('body').removeClass('night-mode');
  }
});

