<template lang="html">
    <div class="main m0a">
        <transition name="fade">
            <div class="write pb100">
                <div class="form-group">
                    <input type="text" id="title" class="form-control input-lg no-border no-shadow size28 bold" autofocus autocomplete="off" placeholder="Tiêu đề"  @keyup="typing" v-model="form.title">
                </div>
                <div class="form-group">
                    <textarea rows="10" id="content" class="form-control no-border no-shadow relative size16" placeholder="Nội dung"  @keyup="typing" v-model="form.content"></textarea>
                </div>
            </div>
        </transition>
        <div class="write-toolbar">
            <div class="container">
                <button data-toggle="modal" data-target="#form-modal" type="button" :disabled="isEmpty" :class="{disabled : isLoading}" class="btn button-success pull-right minw100">
                    <span v-if="!isLoading"><i class="iconfont ic-successed"></i> Đăng bài</span>
                    <span v-else><i class="iconfont ic-search-change ic-spinner"></i> Đang lưu</span>
                </button>
                <a @click="remove_draft" v-if="!isEmpty" class="btn button-danger pull-right mr20"><i class="fa fa-trash"></i> Xóa bản nháp</a>
            </div>
        </div>
        <form-modal @choose="choosed" :current="form.thumbnail" :attachments="form.attachments"></form-modal>
    </div>
</template>

<script>
import MediumEditor from 'medium-editor';
import MediumInsert from 'medium-editor-insert-plugin';
window.MediumInsert = MediumInsert.MediumInsert;
import a from '../../../../../../public/plugins/medium-editor/css/medium-editor.css'
import b from '../../../../../../public/plugins/medium-editor/css/themes/default.css'
import c from '../../../../../../public/plugins/medium-editor/insert-plugins/css/medium-editor-insert-plugin.min.css'

import { post, get } from '../../../api'

import FormModal from './FormModal'

export default {
    props: ['slug', 'draft'],
    components: {
            FormModal
    },
    data() {
        return {
            form: {
                title: '',
                content: '',
                attachments: [],
                thumbnail: '',
                slug: null
            },
            store: {
                url: '/api/v1/draft'
            },
            typingTimer: null,
            isEmpty: true,
            isLoading: false,
            editor: null
        }
    },
    watch: {
        'form.title': 'is_Empty',
        'form.content': 'is_Empty',
    },
    mounted() {
        let vm = this
        if(this.slug) {
            this.fetchData()
        };
        this.editor = new MediumEditor('#content', {
            buttonLabels: 'fontawesome',
            contentWindow: window,
            spellcheck: false,
            placeholder: {
                 text: 'Nhập nội dung văn bản',
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
        $('#content').mediumInsert({
           editor: this.editor,
           imageDragging: false,
           addons: {
             images: {
                 label: '<span class="fa fa-camera"></span>',
                 uploadScript: null,
                //  deleteScript: "/media/delete",
                 deleteScript: function(file) {
                    vm.isLoading = true
                    post(`/api/v1/media/delete`, {
                        file: file
                    }).then(resp => {
                        if(resp.data.status == 'deleted') {
                            vm.form.attachments = vm.form.attachments.filter(function(item) {
                                return item !== resp.data.attachment_id
                            })
                            vm.isLoading = false
                        }
                    })
                 },
                 deleteMethod: 'POST',
                 fileDeleteOptions: {
                    url: "/api/v1/media/delete",
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
                    url: "/api/v1/media/upload",
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
                     vm.form.attachments.push(data.result.files[2].attachment_id);
                     var $img = $el.find('img');
                     $img.attr('data-original', data.result.files[0].url);
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
                 oembedProxy: "/api/v1/media/embed",
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
        this.editor.subscribe('editableInput', function (event, editable) {
           vm.typing()
        });

    },
    methods: {
        choosed(data) {
           this.form.thumbnail = data
        },
        is_Empty() {
            this.isEmpty = false
        },
        fetchData() {
            var url = `/${this.slug}.html`
            get(url)
            .then(resp => {
                if(resp.data.status == 'error') {
                    this.removed_draft();
                }else{
                    this.form.title = resp.data.title
                    this.form.content = resp.data.content
                    this.form.thumbnail = resp.data.thumbnail
                    this.form.attachments = []
                    if(resp.data.attachments) {
                        resp.data.attachments.forEach(data => {
                            if(typeof data === 'object') {
                                this.form.attachments.push(data.id)
                            }else{
                                this.form.attachments.push(data)
                            }
                        })
                        // this.form.attachments = resp.data.attachments
                    }
                    this.form.slug = this.slug
                    this.editor.setContent(this.form.content)
                    this.store.url = '/api/v1/update'
                }
            })
        },
        typing() {
            clearTimeout(this.typingTimer);
            if(this.form.title || this.editor_get_content(this.editor.serialize())) {
                this.typingTimer = setTimeout(this.doneTyping, 2000);
            }
        },
        doneTyping () {
            this.isLoading = true
            this.form.content = this.editor_get_content(this.editor.serialize());
            post(this.store.url, this.form)
            .then(resp => {
                this.form.title = resp.data.data.title
                this.form.content = resp.data.data.content
                this.form.slug = resp.data.data.slug
                window.history.pushState("object or string", "Title", "/"+this.form.slug+"/edit");
                this.isLoading = false
            })
        },
        editor_get_content(content) {
           for (var key in content) if (content.hasOwnProperty(key)) break;
           return content[key].value
        },
        save() {
            if(this.form.title && this.form.content) {
                post('/api/v1/write', this.form)
                .then(resp => {
                    if(resp.data.status == 'success') {
                        window.location.href=`/${resp.data.url}`
                    }
                })
            }
        },
        remove_draft() {
            let vm = this;
            swal({
                title: 'Xóa bản nháp?',
                html: 'Bản nháp sau khi xóa sẽ không thể khôi phục lại.<br> Bạn vẫn muốn xóa?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Vâng, xóa'
             }).then(function () {
                 vm.removed_draft();
             }).catch(swal.noop);//end then swal
        },
        removed_draft() {
            post('/api/v1/remove_draft')
            .then(resp => {
                if(resp.data.status == 'removed') {
                    window.location.href=`/write`
                }
            });
        }
    }
}
</script>

<style lang="css">
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0
}
.write-toolbar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50px;
    line-height: 50px;
    background: rgba(255,255,255,0.7);
    padding: 7px 0;
}
.write {
    margin-bottom: 100px;
}
#title {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    border-radius: 2px;
    transition: box-shadow 200ms cubic-bezier(0.4, 0.0, 0.2, 1);
    height: 55px;
}
#title:focus {
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.08);
}
</style>
