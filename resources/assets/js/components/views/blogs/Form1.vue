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
            </div>
        </div>
        <form-modal @choose="choosed" :attachment_ids="form.attachment_ids"></form-modal>
    </div>
</template>

<script>
import MediumEditor from 'medium-editor';
import MediumInsert from 'medium-editor-insert-plugin';
window.MediumInsert = MediumInsert.MediumInsert;
import a from '../../../../../../public/plugins/medium-editor/css/medium-editor.css'
import b from '../../../../../../public/plugins/medium-editor/css/themes/default.css'
import c from '../../../../../../public/plugins/medium-editor/insert-plugins/css/medium-editor-insert-plugin.min.css'

import { get, post } from '../../../api'

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
                attachment_ids: [],
                thumbnail: '',
                slug: null
            },
            store: {
                url: '/draft'
            },
            typingTimer: null,
            isEmpty: true,
            isLoading: false,
            editor: null
        }
    },
    watch: {
        'form.title': 'is_Empty',
        'form.contant': 'is_Empty',
        'form.slug': 'changeUrlUpdate'
    },
    mounted() {
        let vm = this
        if(this.slug) {
            this.fetchData()
        };
        this.changeUrlUpdate();
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
                    post(`/media/delete`, {
                        file: file
                    }).then(resp => {
                        if(resp.data.status == 'deleted') {
                            vm.form.attachment_ids = vm.form.attachment_ids.filter(function(item) {
                                return item !== resp.data.attachment_id
                            })
                            vm.isLoading = false
                        }
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
                     vm.form.attachment_ids.push(data.result.files[2].attachment_id);
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
        changeUrlUpdate() {
            if(this.form.slug) {
                this.store.url = '/update/' + this.form.slug
            }
        },
        fetchData() {
            var url = `/${this.slug}/edit`
            if(this.draft) {
                url = `/draft/${this.slug}`
            }
            post(url)
            .then(resp => {
                this.form.title = resp.data.title
                this.form.content = resp.data.content
                this.form.attachment_ids = resp.data.attachment_ids
                this.editor.setContent(this.form.content)
                this.store.url = '/update/' + this.slug
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
                this.form.slug = resp.data.slug
                this.form.title = resp.data.data.title
                this.form.content = resp.data.data.content
                if(this.draft) {
                    window.history.pushState("object or string", "Title", "/draft/"+this.form.slug);
                }
                window.history.pushState("object or string", "Title", "/"+this.form.slug+".html/edit");
                this.isLoading = false
            })
        },
        editor_get_content(content) {
           for (var key in content) if (content.hasOwnProperty(key)) break;
           return content[key].value
        },
        save() {
            if(this.form.title && this.form.content) {
                // var formData = new FormData();
                // formData.append('title' ,this.form.title);
                // formData.append('content', this.form.content);
                // formData.append('slug', this.data.slug);
                // formData.append('thumbnail', this.data.thumbnail);
                // formData.append('attachment_ids', this.data.attachment_ids);
                post('/write', this.form)
                .then(resp => {
                    if(resp.data.status == 'success') {
                        window.location.href=`/${resp.data.url}`
                    }
                })
            }
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
