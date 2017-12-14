<template>
	<div class="article-quick-view">
		<div v-if="show" class="quickview-content">
			<header @click.stop="minimize = !minimize" class="quick-view-header">
				<h4 class="quick-view-title">
					<a :href="'/'+article.slug + '-' + article.id + '.html'">Xem bài viết</a>
					<span @click="show = false">&times;</span>
				</h4>
			</header>
			<div v-if="!minimize" class="quick-view-body scroller" :style="{ 'height': winHeight + 'px' }">
				<h4 class="detail-title">{{ article.title }}</h4>
				<div class="detail-content" v-html="article.content"></div>
				<!-- COMMENT BOX -->
		        	<comment-box :post_id="article.id"></comment-box>
		        <!-- COMMENT BOX -->
			</div>		
		</div>		
	</div>
</template>
<script>
import { post, get } from "../../api";
export default {
  data() {
    return {
      article: [],
      winHeight: 0,
      show: false,
      minimize: false
    };
  },
  mounted() {
    let vm = this;
    this.fetchData();
    this.getWinSize();
    this.$nextTick(function() {
      window.addEventListener("resize", vm.getWinSize);
    });
  },
  methods: {
    fetchData() {
      get(`/api/v1/article/30`).then(resp => {
        // console.log(resp)
        this.article = resp.data;
      });
    },
    getWinSize() {
      this.winHeight =
        document.documentElement.clientHeight - 120 >= 100
          ? document.documentElement.clientHeight - 120
          : 100;
    }
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.getWinSize);
  }
};
</script>
<style lang="css">

</style>