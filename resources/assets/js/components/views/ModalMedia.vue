<template lang="html">
    <div class="media-modal">
        <div id="edit-avatar" class="avatar-circle">
            <img class="media-object img-circle avatar-outline" :src="'/image/96/96/'+user.avatar" v-if="user.avatar" :alt="user.name" :title="user.name">
            <!-- Button trigger modal -->
            <button type="button" id="edit-avatar-button" class="btn btn-primary btn-lg"  data-tooltip="Thay đổi ảnh đại diện" data-placement="right" data-toggle="modal" data-target="#modal-media">
             <i class="iconfont ic-picture"></i>
            </button>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal-media" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <transition name="fade">
                <!-- FRONTEND -->
                <div v-if="!is_backend" class="modal-content minh300">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Quản lý hình ảnh</h4>
                  </div>
                  <div class="modal-body">
                      <div class="action_area">
                         <div class="upload_file">
                            <span><i class="iconfont ic-follow"></i> Tải ảnh lên</span>
                            <input type="file" @change="upload" id="upload" value="Tải ảnh lên" accept="image/gif,.gif,image/jpeg,.jpg,image/png,.png,.jpeg" :multiple="multiple" />
                         </div>
                         <button type="button" :disabled="!image" @click="edit_avatar" id="cropper_btn" class="btn btn-primary"><i class="fa fa-magic"></i> Chỉnh sửa</button>
                      </div>
                      <div class="image_storage scroller">
                          <div v-if="loading" class="v-loading">
                             <i class="fa fa-spinner fa-pulse fa-fw"></i>
                          </div>
                          <div v-if="data.avatar.length > 0" class="avatar_storage">
                              <h4 class="modal_album_title"><i class="iconfont ic-arrow" aria-hidden="true"></i> Hình ảnh hồ sơ cá nhân <small @click="refresh_avatar" style="cursor:pointer" class="pull-right"><i class="fa fa-refresh"></i> Refresh</small></h4>
                              <ul class="clearfix p0 m0">
                                 <li v-for="item in data.avatar" @click.prevent="toggle_active" :data-image="item.url" data-type="avatar" :class="{ active: item.url == image }" class="photo_item col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                    <img class="img-responsive" :src="((winWidth < 768 && winWidth > 450) ? '/image/150/150/' : '/image/96/96/') + item.url">
                                    <span class="remove_photo" :style="item.url == image ? 'opacity:1' : 'opacity:0'" @click.stop="remove_photo" data-tooltip="Xóa" data-placement="left" >
                                        <i class="iconfont ic-unfollow"></i>
                                    </span>
                                 </li>
                             </ul>
                             <div v-if="avatar_page.last > 1" class="page_nav">
                                 <a style="cursor:pointer" @click="next_avatar" :data-url="'/media/get_media?type=avatar&page='+(avatar_page.current+1)" v-if="avatar_page.current < avatar_page.last">Xem thêm</a>
                             </div>
                          </div>

                          <div v-if="data.photo.length > 0" class="photo_storage">
                              <h4 class="modal_album_title"><i class="iconfont ic-arrow" aria-hidden="true"></i> Hình ảnh khác <small @click="refresh_photo" style="cursor:pointer" class="pull-right"><i class="fa fa-refresh"></i> Refresh</small></h4>
                              <ul class="clearfix p0 m0">
                                 <li v-for="item in data.photo" @click="toggle_active_photo" :data-image="item.url" data-type="photo" :class="{ active: item.url == image }" class="photo_item col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                    <img class="img-responsive" :src="((winWidth < 768 && winWidth > 450) ? '/image/150/150/' : '/image/96/96/') + item.url">
                                    <span class="remove_photo" :style="item.url == image ? 'opacity:1' : 'opacity:0'" @click.stop="remove_photo" data-tooltip="Xóa" data-placement="left" >
                                        <i class="iconfont ic-unfollow"></i>
                                    </span>
                                 </li>
                             </ul>
                             <div v-if="photo_page.last > 1" class="page_nav">
                                 <a style="cursor:pointer" @click="next_photo" :data-url="'/media/get_media?type=photo&page='+(photo_page.current+1)" v-if="photo_page.current < photo_page.last">Xem thêm</a>
                             </div>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" @click="save_total" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
                <!-- FRONTEND -->
                <!-- BACKEND -->
                <div v-else class="modal-content">
                 <div class="modal-header">
                    <button type="button" class="close" @click="close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Chỉnh sửa ảnh đại diện</h4>
                 </div>
                 <div class="modal-body clearfix">

                     <div class="upload-crop minh300">
                        <div class="upload-msg">
                           <!-- loading -->
                           <div v-if="loading" class="v-loading w300 h300 m0a croppie-placeholder">
                              <i class="fa fa-spinner fa-pulse fa-fw"></i>
                           </div>
                           <!-- loading -->
                        </div>
                        <div class="upload-crop-wrap">
                             <div id="upload-crop"></div>
                        </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                    <button type="button" @click="close" class="btn btn-default btn-sm">Hủy</button>
                    <button type="button" @click="save" class="btn btn-primary btn-sm">Lưu</button>
                 </div>
                </div>
                <!-- BACKEND -->
            </transition>
          </div>
        </div>
    </div>
</template>

<script>
import croppie from "../../../../../public/plugins/croppie/croppie.js";
import exif from "../../../../../public/plugins/croppie/exif.js";
import croppiecss from "../../../../../public/plugins/croppie/croppie.css";
import { post, get } from "../../api";
export default {
  props: ["multiple", "mode"],
  data() {
    return {
      data: {
        avatar: [],
        photo: []
      },
      loading: false,
      image: null,
      mycroppie: null,
      is_backend: 0,
      avatar_page: {
        current: 0,
        total: 0,
        last: 0
      },
      photo_page: {
        current: 0,
        total: 0,
        last: 0
      },
      winWidth: 0
    };
  },
  mounted() {
    let vm = this;
    $("#modal-media").on("shown.bs.modal", function() {
      if (
        vm.avatar_page.current == 0 &&
        vm.avatar_page.total == 0 &&
        vm.avatar_page.last == 0
      ) {
        vm.fetchAvatar();
      }
      if (
        vm.photo_page.current == 0 &&
        vm.photo_page.total == 0 &&
        vm.photo_page.last == 0
      ) {
        vm.fetchPhoto();
      }
      vm.$nextTick(function() {
        window.addEventListener("resize", vm.getWinWidth);
        vm.getWinWidth();
      });
      vm.getWinWidth();
    }); //end modal show
  },
  watch: {
    "user.avatar": "set_image"
  },
  computed: {
    user() {
      return this.$store.state.auth_user;
    }
  },
  methods: {
    // WINDOW WIDTH HEIGHT
    getWinWidth() {
      this.winWidth = document.documentElement.clientWidth;
    },
    // WINDOW WIDTH HEIGHT
    edit_avatar() {
      let vm = this;
      if (this.image) {
        this.loading = true;
        this.is_backend = true;
        setTimeout(function() {
          vm.setcroppie("/images/" + vm.image);
        }, 300);
      }
    },
    refresh_avatar() {
      this.avatar_page.current = 0;
      this.avatar_page.total = 0;
      this.avatar_page.last = 0;
      this.fetchAvatar();
    },
    refresh_photo() {
      this.photo_page.current = 0;
      this.photo_page.total = 0;
      this.photo_page.last = 0;
      this.fetchPhoto();
    },
    set_image() {
      this.image = this.user.avatar;
    },
    fetchAvatar() {
      let vm = this;
      this.loading = true;
      this.data.avatar = [];
      if (
        this.avatar_page.current == 0 &&
        this.avatar_page.total == 0 &&
        this.avatar_page.last == 0
      ) {
        get(`/api/v1/media/get_media/?type=avatar`).then(resp => {
          if (resp.data.data) {
            resp.data.data.forEach(function(value) {
              vm.data.avatar.push(value);
            });
            this.avatar_page.current = resp.data.current_page;
            this.avatar_page.total = resp.data.total;
            this.avatar_page.last = resp.data.last_page;
            this.loading = false;
          }
        });
      }
    },
    fetchPhoto() {
      let vm = this;
      this.loading = true;
      this.data.photo = [];
      if (
        this.photo_page.current == 0 &&
        this.photo_page.total == 0 &&
        this.photo_page.last == 0
      ) {
        get(`/api/v1/media/get_media/?type=photo`).then(resp => {
          if (resp.data.data) {
            resp.data.data.forEach(function(value) {
              vm.data.photo.push(value);
            });
            this.photo_page.current = resp.data.current_page;
            this.photo_page.total = resp.data.total;
            this.photo_page.last = resp.data.last_page;
            this.loading = false;
          }
        });
      }
    },
    upload(e) {
      let vm = this;
      const files = e.target.files;
      if (files && files.length > 0) {
        this.loading = true;
        const fileReader = new FileReader();
        fileReader.onload = event => {
          this.image = event.target.result;
          switch (this.mode) {
            case "avatar":
              this.is_backend = 1;
              setTimeout(function() {
                vm.setcroppie(vm.image);
              }, 300);
              break;
            default:
              return;
          } //end switch
        }; //end fileReader
        fileReader.readAsDataURL(files[0]);
      }
    },
    setcroppie(image) {
      let vm = this;
      let el = document.getElementById("upload-crop");
      this.mycroppie = new Croppie(el, {
        enforceBoundary: true,
        enableOrientation: true,
        viewport: {
          width: 300,
          height: 300,
          type: "square"
        },
        boundary: {
          width: 300,
          height: 300
        }
      });
      $(".upload-crop").addClass("ready");
      this.mycroppie
        .bind({
          url: image
        })
        .then(function() {
          console.log("jQuery bind complete");
          vm.loading = false;
        });
    },
    save() {
      let vm = this;
      this.mycroppie
        .result({
          type: "canvas",
          size: "viewport"
        })
        .then(function(resp) {
          var formData = new FormData();
          formData.append("image", resp);
          formData.append("type", "avatar");
          post(`/api/v1/media/upload`, formData).then(response => {
            if (response.data.status == "success") {
              var obj = {
                url: response.data.image,
                type: "avatar"
              };
              vm.data.avatar.unshift(obj);
              vm.image = response.data.image;
              vm.is_backend = false;
            }
            vm.destroy();
          });
        });
    },
    close() {
      this.is_backend = null;
      this.destroy();
    },
    destroy() {
      this.mycroppie.destroy();
      this.is_backend = null;
    },
    toggle_active(e) {
      var url = e.currentTarget.getAttribute("data-image");
      if (
        $(e.target).hasClass("active") ||
        $(e.target)
          .parent()
          .hasClass("active")
      ) {
        this.image = null;
      } else {
        this.image = url;
      }
    },
    toggle_active_photo(e) {
      var url = e.currentTarget.getAttribute("data-image");
      let vm = this;
      if (this.mode == "avatar") {
        this.loading = true;
        this.is_backend = true;
        setTimeout(function() {
          vm.setcroppie("/images/" + url);
        }, 300);
      }
    },
    save_total() {
      switch (this.mode) {
        case "avatar":
          if (this.image) {
            post("/api/v1/update_avatar", {
              avatar: this.image
            }).then(resp => {
              if (resp.data.status == "success") {
                $(".avatar-xs").attr("src", "/images/" + this.image);
                this.$store.commit("update_user_avatar", this.image);
                $("#modal-media").modal("hide");
              }
            });
          }
          break;
        default:
        //   this.current_avatar = this.item_active
        //   $('#modal-media').modal('hide')
        //   //$('.'+this.callback).html(`<img src="/images/${this.item_active}" />`)
        //   this.$emit('choose', this.item_active)
      }
    },
    next_avatar(e) {
      let vm = this;
      var url = e.currentTarget.getAttribute("data-url");
      if (this.avatar_page.current < this.avatar_page.last) {
        this.loading = true;
        get(url).then(resp => {
          resp.data.data.forEach(function(value) {
            vm.data.avatar.push(value);
          });
          this.loading = false;
          vm.avatar_page.current = resp.data.current_page;
          vm.avatar_page.total = resp.data.total;
          vm.avatar_page.last = resp.data.last_page;
        });
      }
    },
    next_photo(e) {
      let vm = this;
      var url = e.currentTarget.getAttribute("data-url");
      if (this.photo_page.current < this.photo_page.last) {
        this.loading = true;
        get(url).then(resp => {
          resp.data.data.forEach(function(value) {
            vm.data.photo.push(value);
          });
          this.loading = false;
          vm.photo_page.current = resp.data.current_page;
          vm.photo_page.total = resp.data.total;
          vm.photo_page.last = resp.data.last_page;
        });
      }
    },
    remove_photo(e) {
      let vm = this;
      var url = e.currentTarget.parentNode.getAttribute("data-image");
      var type = e.currentTarget.parentNode.getAttribute("data-type");
      var message = "";
      var title = "";
      post(`/api/v1/media/check`, {
        image: url
      }).then(response => {
        if (response.data.status == "used") {
          message = `Trong bài viết: <br><a target="_blank" href="${response
            .data.url}">${response.data.title}</a></br> bạn có muốn xóa?`;
          title = "Ảnh đang được sử dụng!";
        } else {
          message = "Bạn muốn xóa ảnh này?";
          title = "Xóa hình ảnh?";
        }
        swal({
          title: title,
          html: message,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Vâng, xóa"
        })
          .then(function() {
            post(`/api/v1/media/delete`, {
              image: url
            }).then(response => {
              if (response.data.status == "deleted") {
                if (type == "avatar") {
                  vm.refresh_avatar();
                } else {
                  vm.refresh_photo();
                }
              }
            }); //end post
          })
          .catch(swal.noop); //end then swal
      }); //end check
    }
  }, //end methods
  beforeDestroy() {
    window.removeEventListener("resize", this.getWinWidth);
  }
};
</script>

<style lang="css">
#edit-avatar:after {
  background: rgba(0, 0, 0, 0.67);
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  content: "";
  border-radius: 50%;
  z-index: 1;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}
#edit-avatar-button {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #dadada;
  border: none;
  border-radius: 2px;
  width: 40px;
  height: 30px;
  padding: 0 5px;
  text-align: center;
  line-height: 30px;
  z-index: 2;
  color: #6d6d6d;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}
#edit-avatar:hover:after,
#edit-avatar:hover #edit-avatar-button {
  opacity: 1;
  transition: opacity 0.3s ease-in-out;
}

.action_area {
  display: flex;
  border: 1px solid #e5e5e5;
}
.action_area .upload_file:hover,
.action_area #cropper_btn:hover {
  background: #f5f5f5;
}
.action_area .upload_file,
.action_area #cropper_btn {
  width: 50%;
  height: 80px;
  position: relative;
  background: #fafafa;
  border: none;
  outline: none;
  box-shadow: none;
  color: #333;
  font-weight: bold;
  cursor: pointer;
}
.upload_file {
  border-right: 1px solid #e5e5e5 !important;
}
.upload_file span {
  position: absolute;
  line-height: 80px;
  text-align: center;
  display: block;
  width: 100%;
}
.upload_file input {
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  opacity: 0;
  cursor: pointer;
}
.no-image {
  text-align: center;
  font-size: 20px;
  color: #dedcdc;
}
.croppie-container .cr-slider-wrap {
  max-width: 280px;
}
#cropper_btn[disabled] {
  opacity: 0.1;
  cursor: default;
}
.media {
  overflow: visible;
}
/*Page NAV*/
.page_nav {
  display: block;
  text-align: right;
  padding-right: 0;
}
.page_nav > a {
  font-size: 13px;
  font-weight: bold;
  color: #1e88e5;
}
h4.modal_album_title {
  font-size: 14px;
  font-weight: bold;
}
.photo_storage {
  padding-top: 10px;
  margin-top: 10px;
  border-top: 1px solid #eee;
}
li.photo_item > span {
  position: absolute;
  top: 0;
  right: 2px;
  text-shadow: -1px 0 #828282, 0 1px #828282, 1px 0 #828282, 0 -1px #828282;
  color: #fff;
  font-weight: normal;
  transition: opacity 0.3s ease-in-out;
  -webkit-transition: opacity 0.3s ease-in-out;
  -moz-transition: opacity 0.3s ease-in-out;
  -ms-transition: opacity 0.3s ease-in-out;
  -o-transition: opacity 0.3s ease-in-out;
}
li.photo_item:hover > span {
  opacity: 1 !important;
  transition: opacity 0.3s ease-in-out;
  -webkit-transition: opacity 0.3s ease-in-out;
  -moz-transition: opacity 0.3s ease-in-out;
  -ms-transition: opacity 0.3s ease-in-out;
  -o-transition: opacity 0.3s ease-in-out;
}
li.photo_item:hover > img {
  opacity: 0.6;
  transition: opacity 0.3s ease-in-out;
  -webkit-transition: opacity 0.3s ease-in-out;
  -moz-transition: opacity 0.3s ease-in-out;
  -ms-transition: opacity 0.3s ease-in-out;
  -o-transition: opacity 0.3s ease-in-out;
}
.remove_photo:before {
  text-shadow: none;
  font-weight: bold;
  min-width: 50px;
}
.remove_photo:after {
  margin-left: 2px;
}
</style>
