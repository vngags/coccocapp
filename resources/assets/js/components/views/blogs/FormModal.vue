<template lang="html">
    <div class="modal fade" id="form-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Ảnh đại diện và từ khóa tìm kiếm</h4>
          </div>
          <div class="modal-body p30">
            <h5 class="bold text-uppercase">Lựa chọn ảnh đại diện <small class="pull-right text-nomal">(Tìm thấy {{ images.length }} ảnh)</small></h5>
            <ul class="article_images clearfix p0 list-inline ml0">
                <li v-for="image in images" @click="toggle_active" :data-image="image.url" :class="{ active: image.url == current_thumbnail }" class="photo_item col-lg-6 col-md-6 col-sm-6 col-xs-8">
                    <img :src="'/image/135/135/'+image.url" class="img-responsive">
                </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" @click="$parent.save()" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import { get, post } from '../../../api'
export default {
    props: ['attachments', 'current'],
    data() {
        return {
            images: [],
            current_thumbnail: null,
        }
    },
    watch: {
        'current': 'set_current_thumbnail'
    },
    mounted() {
        let vm = this
        $('#form-modal').on('shown.bs.modal', function () {
            vm.fetchAttachments()
        })//end modal show
    },
    methods: {
        set_current_thumbnail() {
            this.current_thumbnail = this.current.replace('_293_184', '')
        },
        fetchAttachments() {
            if(this.attachments) {
                post('/api/v1/media/get_attachments', {
                    attachments: this.attachments
                })
                .then(resp => {
                    this.images = resp.data
                })
            }
        },
        toggle_active(e) {
           var url = e.currentTarget.getAttribute('data-image')
           if($(e.target).hasClass('active')) {
              this.current_thumbnail = null
           }else{
              this.current_thumbnail = url
              this.$emit('choose', url)
           }
        },
    },//end methods
}
</script>

<style lang="css">
</style>
