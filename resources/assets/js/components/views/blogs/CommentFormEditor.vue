<template lang="html">
  <div class="new_comment">

      <div class="author mb10 relative">
          <div class="author_hover">
              <div class="avatar pull-left">
                  <a :href="'/u/'+user.slug" class="avatar-circle">
                      <img :src="'/image/24/24/'+user.avatar" class="img-circle" width="24">
                  </a>
              </div>
              <div class="info ml30 lh24 relative">
                  <div class="author_info">
                      <a :href="'/u/'+user.slug" class="author_name bold">
                         {{ user.name }}
                      </a>
                  </div>
              </div>
          </div>
      </div>

      <!-- <div class="avatar w40 pull-left">
          <a :href="user.slug ? '/u/' + user.slug : 'javascript:void(0)'" class="avatar-circle">
              <img v-if="!user.id" src="/images/default-avatar.png" width="40" class="img-circle">
              <img v-else :src="'/image/40/40/' + user.avatar" class="img-circle" :alt="user.name" :title="user.name">
          </a>
      </div> -->

      <div class="form relative">
          <div class="form-comment">
              <textarea name="name" id="comment_text" class="form-control full-text-editor" placeholder="Viết bình luận" v-model="message"></textarea>
              <p class="clearfix">
                  <button @click="save" type="button" class="btn button-primary pull-right">Gửi bình luận</button>
              </p>
          </div>
      </div>

  </div>
</template>

<script>
import MediumEditor from 'medium-editor';
import MediumInsert from 'medium-editor-insert-plugin';
window.MediumInsert = MediumInsert.MediumInsert;
// import a from '../../../../../../public/plugins/medium-editor/css/medium-editor.css'
// import b from '../../../../../../public/plugins/medium-editor/css/themes/default.css'
// import c from '../../../../../../public/plugins/medium-editor/insert-plugins/css/medium-editor-insert-plugin.min.css'
export default {
    data() {
        return {
            message: null,
            editor: null
        }
    },
    mounted() {
        let vm = this
        this.editor = new MediumEditor('#comment_text', {
            buttonLabels: 'fontawesome',
            contentWindow: window,
            spellcheck: false,
            placeholder: {
                 text: 'Viết bình luận',
                 hideOnClick: true
             },
             toolbar: {
                 buttons: ['bold', 'italic', 'underline','anchor', 'quote','justifyLeft','justifyCenter','justifyRight', 'h2', 'h3'],
             },
             anchor: {
                 customClassOption: null,
                 customClassOptionText: 'Button',
                 linkValidation: true,
                 placeholderText: 'Dán hoặc gõ liên kết vào đây',
                 targetCheckbox: true,
                 targetCheckboxText: 'Mở trong cửa sổ mới'
             },
        });//editor
        $('#comment_text').mediumInsert({
           editor: this.editor,
           imageDragging: false,
           addons: {
             images: {
                 label: '<span class="fa fa-camera"></span>',
                 uploadScript: null,
                //  deleteScript: "/media/delete",
                 deleteScript: function(file) {
                    post(`/media/delete`, {
                        file: file
                    }).then(resp => {
                        // if(resp.data.status == 'deleted') {
                        //     vm.form.attachments = vm.form.attachments.filter(function(item) {
                        //         return item !== resp.data.attachment_id
                        //     })
                        //     vm.isLoading = false
                        // }
                    })
                 },
                 deleteMethod: 'POST',
                 fileDeleteOptions: {
                    url: "/media/delete",
                    type: 'POST',
                    headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                 },
                 preview: false,
                 captions: true,
                 captionPlaceholder: 'Nhập chú thích vào đây (tùy chọn)',
                 autoGrid: 3,

                 fileUploadOptions: {
                    formData: {},
                    url: "/media/upload",
                    type: "POST",
                    paramName:"image",
                    multipart: true,
                    maxFileSize: 4000000,
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                    headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                 },
                 styles: {
                     wide: {
                         label: '<span class="fa fa-align-justify"></span>',
                         added: function ($el) {},
                         removed: function ($el) {}
                     },
                     left: {
                         label: '<span class="fa fa-align-left"></span>'
                     },
                     right: {
                         label: '<span class="fa fa-align-right"></span>'
                     },
                     grid: {
                         label: '<span class="fa fa-th"></span>'
                     }
                 },
                 actions: {
                     remove: {
                         label: '<span class="fa fa-times"></span>',
                         clicked: function ($el) {
                             var $event = $.Event('keydown');
                             $event.which = 8;
                             $(document).trigger($event);
                         }
                     }
                 },
                 messages: {
                     acceptFileTypesError: 'This file is not in a supported format: ',
                     maxFileSizeError: 'This file is too big: '
                 },
                 uploadCompleted: function ($el, data) {
                    //  vm.form.attachments.push(data.result.files[2].attachment_id);
                    //  var $img = $el.find('img');
                    //  $img.attr('data-original', data.result.files[0].url);
                 },
                 uploadFailed: function (uploadErrors, data) {
                    swal(
                      'Oops...',
                      'Có lỗi trong quá trình tải ảnh lên',
                      'error'
                    )
                },
             },
             embeds: {
                 label: '<span class="fa fa-youtube-play"></span>',
                 placeholder: 'Dán URL YouTube, Vimeo, Facebook, Twitter or Instagram vào đây và nhấn Enter',
                 captions: true,
                 captionPlaceholder: 'Nhập chú thích vào đây (tùy chọn)',
                 oembedProxy: "/media/embed",
                 styles: {
                     left: {
                         label: '<span class="fa fa-align-left"></span>'
                     },
                     wide: {
                        label: '<span class="fa fa-align-justify"></span>',
                        added: function ($el, data) {
                           console.log(data);
                        },
                        removed: function ($el) {}
                     },
                     right: {
                         label: '<span class="fa fa-align-right"></span>'
                     }
                 },
                 actions: {
                     remove: {
                         label: '<span class="fa fa-times"></span>',
                         clicked: function ($el) {
                             var $event = $.Event('keydown');
                             $event.which = 8;
                             $(document).trigger($event);
                         }
                     }
                 }
             }
         }
        });
    },
    computed: {
       user() {
          return this.$store.state.auth_user
       }
    },//end computed
    methods: {
        save() {
            if(this.message || this.editor_get_content(this.editor.serialize())) {
                this.$emit('onSubmit', {
                    type: 'parent',
                    message: this.editor_get_content(this.editor.serialize())
                });
                this.message = ''
                this.editor.setContent('')
            }
        },
        editor_get_content(content) {
           for (var key in content) if (content.hasOwnProperty(key)) break;
           return content[key].value
        },
    }
}
</script>

<style lang="css">
.form-comment > .full-text-editor {
    min-height: 100px;
}
</style>
