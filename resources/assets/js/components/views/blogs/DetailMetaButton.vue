<template lang="html">
    <div class="article-bottom-meta clearfix">
        <div class="pull-left">
            <div class="like flexbox">
                <a class="like-button" @click="checked" :class="{ 'liked' : active }">
                    <span class="like-icon">
                        <div class="heart-animation-1"></div>
                        <div class="heart-animation-2"></div>
                    </span>
                    Thích
                </a>
                <span class="like-count">154</span>
            </div>
        </div>
        <div class="pull-right">
            <ul class="list-inline">
                <li>
                    <a class="btn button-default save-article"><i class="fa fa-bookmark-o"></i> Lưu | 12</a>
                </li>
            </ul>
        </div>


        <!-- <a class="like-button" @click="checked" :class="{ 'liked' : active }">
              <!-- <a class="btn">
                  <svg>
                    <defs>
                        <linearGradient id="grad1">
                            <stop offset="0%" stop-color="#FF8282"/>
                            <stop offset="100%" stop-color="#E178ED" />
                        </linearGradient>
                    </defs>
                     <rect x="5" y="5" rx="25" fill="none" stroke="url(#grad1)" width="266" height="50"></rect>
                  </svg>
                  <!--<span>Voir mes réalisations</span>-->
                    <!-- <span>Welcome in my world</span>
            </a>  --
            <span class="like-icon">
                <div class="heart-animation-1"></div>
                <div class="heart-animation-2"></div>
            </span>
            Thích
        </a> -->
        <audio id="article_like_audio">
            <source src="/audio/comment/all-eyes-on-me.mp3">
            <source src="/audio/comment/all-eyes-on-me.ogg">
            <source src="/audio/comment/all-eyes-on-me.m4r">
       </audio>      
    </div>
</template>

<script>
export default {
    data() {
        return {
            active: false
        }
    },
    methods: {
        checked() {
            this.active = !this.active
            document.getElementById("article_like_audio").play()
        }
    }
}
</script>

<style lang="css">
</style>
