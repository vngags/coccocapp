@extends('layouts.app')
@section('title', 'Trang cá nhân của '.$user->name)
@section('content')
@include('layouts.blocks.__navbar')
<div class="container pt90">
    <div class="row">
        <div class="col-md-16">
            <div class="panel no-shadow">
                <div class="profile-info">
                    <div class="media">
                        <div class="profile-cover" style="background-image:url({{ url('image/96/96/'. $user->avatar) }})"></div>
                        <div class="media-left minw100">
                            @if(Auth::check() && Auth::user()->id === $user->id)
                                <modal-media :mode="`avatar`" :current="`{{ $user->avatar }}`" :is_crop="true" :is_multiple="false"></modal-media>
                            @else
                                <div class="avatar-circle">
                                    <img class="media-object img-circle avatar-outline" src="{{ url('image/96/96/'. $user->avatar) }}" alt="{{ $user->name }}" title="{{ $user->name }}">
                                </div>
                            @endif
                         </div>
                          <div class="media-body">
                              <h3 class="media-heading bold">
                                  {{ $user->name }}
                                  @if($user->gender == 'male')
                                    <span data-tooltip="Giới tính Nam" data-placement="right"><i class="iconfont ic-man"></i></span>
                                  @else
                                    <span data-tooltip="Giới tính Nữ" data-placement="right"><i class="iconfont ic-woman"></i></span>
                                  @endif
                              </h3>
                              <p class="">
                                  <small>
                                  <a href="{{ route('user.index', ['slug' => $user->slug]) }}" target="_blank">
                                      <i class="fa fa-external-link"></i>
                                       coccoc.me/u/{{ $user->slug }}
                                  </a>
                                  </small>
                              </p>
                              <ul class="list-inline mb0">
                                  <li>
                                     <div data-tooltip="Số bài viết" data-placement="right">
                                         <i class="iconfont ic-articles"></i> {{ count($posts) }}
                                     </div>
                                  </li>
                                  <li>
                                      <div data-tooltip="Số điểm" data-placement="top">
                                          <i class="iconfont ic-candy"></i> {{ count($posts) }}
                                      </div>
                                  </li>
                                  <li>
                                      <div data-tooltip="Đang theo dõi" data-placement="top">
                                          <i class="iconfont ic-followed"></i> {{ count($user->followings) }}
                                      </div>
                                  </li>
                                  <li>
                                     <div data-tooltip="Số người theo dõi" data-placement="top">
                                         <i class="iconfont ic-follows"></i> {{ count($user->followers) }}
                                     </div>
                                  </li>
                              </ul>
                          </div>
                    </div>
                    <ul class="profile-qrcode">
                        <li class="dropdown">
                            <a class="dropdown-toggle pr0i pointer" id="show-qrcode" data-tooltip="QRCODE" data-placement="top"><i class="iconfont ic-qrcode"></i></a>
                            <ul class="dropdown-menu with-arrow with-arrow-center p20">
                                <div class="qrcode">
                                    <img id="qrcode-logo" class="lazyloaded" src="{{ url('image/96/96/' . $user->avatar) }}" style="display:none">
                                    <div class="qr-code"></div>
                                </div>
                            </ul>
                        </li>
                    </ul>
                    @if(Auth::check() && Auth::user()->id !== $user->id)
                        <div id="profile-follow">
                            @if(in_array(Auth::user()->id, $user->followers_ids))
                                <a href="{{ url('chat/' . $user->code) }}" data-tooltip="Chat với {{ $user->name }}" data-placement="bottom" class="btn button-transparent btn-sm mr5 bold size13"><i class="fa fa-comments-o"></i> Chat</a>
                            @else
                                <a href="{{ url('chat/' . $user->code) }}" data-tooltip="Gửi tin nhắn cho {{ $user->name }}" data-placement="bottom" class="btn button-transparent btn-sm mr5 bold size13"><i class="fa fa-comments-o"></i> Nhắn tin</a>
                            @endif
                            <follow :member_id="{{ $user->id }}" class="pull-right"></follow>
                        </div>
                    @else
                        <div id="profile-follow">
                            <a href="/login" data-tooltip="Chat với {{ $user->name }}" data-placement="bottom" class="btn button-transparent btn-sm mr5 bold size13"><i class="fa fa-comments-o"></i> Chat</a>
                            <follow :member_id="{{ $user->id }}" class="pull-right"></follow>
                        </div>
                    @endif
                </div>
                <ul class="list-inline-menu" id="profile-menu">
                    <li class="active"><a href="#"><i class="iconfont ic-articles"></i> <span>Bài viết mới</span></a></li>
                    <li><a href="#"><i class="iconfont ic-feed"></i> <span>Dòng thời gian</span></a></li>
                    <li><a href="#"><i class="iconfont ic-hot"></i> <span>Nổi bật nhất</span></a></li>
                </ul>
                <div id="articles-container" class="form-group">
                    @foreach($posts as $post)
                        @include('layouts.blocks.__article_content_without_author')                        
                    @endforeach
                </div>

            </div>
        </div>
        <div class="col-md-7 col-md-offset-1 right-column">
            <div class="profile-quote with-border-bottom mb10">
                <h4 class="size13 text-uppercase bold">
                    <i class="iconfont ic-info"></i> Giới thiệu
                    @if(Auth::check() && Auth::user()->id === $user->id)
                        <ul class="pull-right">
                            <li class="dropdown no-list-style">
                                <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    <img class="three-dots-sm pointer" src="{{ asset('images/svg/three-dots-menu.svg') }}" alt="">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a id="profile-quote-edit-btn" class="boldi text-capitalize block pointer">
                                            Chỉnh sửa
                                            <small class="normali text-capitalize block">Giới thiệu hay châm ngôn</small>
                                       </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endif
                </h4>
                @if(isset($user->profile->quote))
                    <blockquote>
                        {{ $user->profile->quote }}
                    </blockquote>
                @else
                    @if(Auth::check() && Auth::user()->id === $user->id)
                        <p id="profile-quote-edit-alert" class="text-brown text-center">Viết gì đó vào đây</p>
                    @endif
                @endif
                <form id="profile-quote-edit-form" action="{{ route('profile.update') }}?type=quote" method="post" style="display:none;">
                    {{ csrf_field() }}
                    <div class="text">
                        <textarea name="quote" rows="3" class="form-control no-resize" placeholder="Viết vài dòng giới thiệu">{{ isset($user->profile->quote) ? $user->profile->quote : '' }}</textarea>
                    </div>
                    <div class="button align-right">
                        <button type="button" id="profile-quote-edit-cancel" class="btn btn-default inline-block btn-xs">Hủy</button>
                        <button type="submit" class="btn btn-primary inline-block btn-xs bold">Lưu</button>
                    </div>
                </form>
            </div>

            <!-- FOLLOWING -->
            <div class="following with-border-bottom">
                <h4 class="size13 text-uppercase bold"><i class="iconfont ic-followed"></i> Đang theo dõi <span class="badge">{{ count($user->followings) }}</span></h4>
                <ul class="list-inline text-center">
                    @foreach($user->followings as $following)
                        <li data-tooltip="{{$following->name}}" data-placement="top">
                            <a href="{{ route('user.index', ['slug' => $following->slug]) }}" class="avatar-circle avatar-outline">
                                <img src="{{ url('image/32/32/' . $following->avatar) }}" class="img-circle">
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- FOLLOWING -->

            <!-- FOLLOWER -->
            <div class="followers with-border-bottom">
                <h4 class="size13 text-uppercase bold"><i class="iconfont ic-follows"></i> Người Theo dõi <span class="badge">{{ count($user->followers) }}</span></h4>
                <ul class="list-inline text-center">
                    @foreach($user->followers as $follower)
                        <li data-tooltip="{{$follower->name}}" data-placement="top">
                            <a href="{{ route('user.index', ['slug' => $follower->slug]) }}" class="avatar-circle avatar-outline">
                                <img src="{{ url('image/32/32/' . $follower->avatar) }}" class="img-circle">
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- FOLLOWER -->

        </div>
    </div>
</div>
<!-- FIXED LIST INLINE MENU -->
<div class="list-inline-menu-fixed">
    <div class="container">
        <div class="row">
            <div class="col-md-16">
                <ul id="profile-menu">
                    <li class="active"><a href="#"><i class="iconfont ic-articles"></i> <span>Bài viết mới</span></a></li>
                    <li><a href="#"><i class="iconfont ic-feed"></i> <span>Dòng thời gian</span></a></li>
                    <li><a href="#"><i class="iconfont ic-hot"></i> <span>Nổi bật nhất</span></a></li>
                    @if(Auth::check() && Auth::user()->id === $user->id)
                    <li class="dropdown pull-right">
                        <a href="#" class="pl0i pr0i" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            <img src="{{ asset('images/svg/three-dots-menu.svg') }}" alt="">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Chỉnh sửa trang cá nhân</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- FIXED LIST INLINE MENU -->
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    setTimeout(function() {
        $('.qr-code').qrcode({
            // render method: 'canvas', 'image' or 'div'
            render: 'image',
            // version range somewhere in 1 .. 40
            minVersion: 1,
            maxVersion: 40,
            // error correction level: 'L', 'M', 'Q' or 'H'
            ecLevel: 'H',
            // offset in pixel if drawn onto existing canvas
            left: 0,
            top: 0,
            // size in pixel
            size: 160,
            // code color or image element
            fill: '#000',
            // background color or image element, null for transparent background
            background: null,
            // content
            text: "{{ route('user.index', ['slug' => $user->slug]) }}",
            // corner radius relative to module width: 0.0 .. 0.5
            radius: 0,
            // quiet zone in modules
            quiet: 3,
            // modes
            // 0: normal
            // 1: label strip
            // 2: label box
            // 3: image strip
            // 4: image box
            mode: 4,
            mSize: 0.3,
            mPosX: 0.5,
            mPosY: 0.5,
            label: "{{ $user->name }}",
            fontname: 'sans',
            fontcolor: '#000',
            image: $('#qrcode-logo')[0]
        });
    }, 100);
    $('#show-qrcode').on('click', function() {
        swal({
          html: "Quét mã QR của {{ $user->name }}<br>để theo dõi trên Cốc Cốc",
          customClass: 'maxw300i',
          imageUrl: $('.qr-code > img').attr('src'),
          imageWidth: 160,
          imageHeight: 160,
          imageAlt: 'QRCODE',
          animation: true
        })
    });
    $('#profile-quote-edit-btn, #profile-quote-edit-alert').on('click', function() {
        $('.profile-quote').addClass('editable');
        $('.profile-quote.editable form textarea').focus();
    });
    $('#profile-quote-edit-cancel').on('click', function() {
        $('.profile-quote').removeClass('editable');
    });


    $(document).on('scroll', function() {
        if($(this).scrollTop() >= ($('#profile-menu').position().top + $('#profile-menu').height() + 60)){
            $(".list-inline-menu-fixed").addClass('is_active');
        }else{
            $(".list-inline-menu-fixed").removeClass('is_active');
        }
    })

})
</script>
@endsection

@section('style')
<style media="screen">
.is_active.list-inline-menu-fixed {
    -webkit-transform: translate(0,0) translateZ(0);
    -khtml-transform: translate(0,0) translateZ(0);
    transform: translate(0,0) translateZ(0);
}
.list-inline-menu-fixed {
    position: fixed;
    top: 60px;
    transition: all .3s;
    z-index: 1029;
    height: 36px;
    -webkit-transform: translate(0,-100%) translateZ(0);
    -khtml-transform: translate(0,-100%) translateZ(0);
    transform: translate(0,-100%) translateZ(0);
    width: 100%;
}
.list-inline-menu-fixed  #profile-menu {
    margin-bottom: 0;
    padding-bottom: 0;
    border: none;
    border-bottom: 1px solid #f0f0f0;
    background: #fff;
    list-style: none;
    padding-left: 0;
}
.list-inline-menu-fixed  #profile-menu > li {
    padding: 4px 0;
    position: relative;
    display: inline-block;
    margin-bottom: -1px;
}
.list-inline-menu-fixed  #profile-menu > li.active > a,
.list-inline-menu-fixed  #profile-menu > li > a:hover {
    color: #646464;
}
.list-inline-menu-fixed  #profile-menu > li > a {
    padding: 7px 20px;
    font-size: 15px;
    font-weight: 700;
    color: #969696;
    line-height: 25px;
}
.profile-info {
    position: relative;
}
.profile-info .media-left, .profile-info .media-right, .profile-info .media-body {position: relative;}
.profile-info .media-heading small a,
.profile-info .list-inline {
    /*text-shadow: -1px 0 1px #777, 0 1px 1px #777, 1px 0 1px #777, 0 -1px 1px #777;*/
    color: #fff;
}
.profile-qrcode {
    margin: 0;
    padding: 0;
    list-style: none;
    position: absolute;
    bottom: 0;
    right: 0;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
}
.profile-qrcode > li > a {
    color: #fff;
}
.profile-qrcode .qrcode {
    width: 160px;
    height: 160px;
}
.profile-quote > blockquote {
    padding: 0;
    margin: 0 0 22px;
    font-size: 17.5px;
    border-left: none;
    font-size: 1em;
    color: #333;
}
.profile-quote > blockquote:before,
.profile-quote > blockquote:after {
    color: #777;
    font-size: 4em;
    line-height: 0;
    vertical-align: -0.5em;
    font-family: Georgia;
}
.profile-quote > blockquote:before {content: open-quote;}
.profile-quote > blockquote:after {content: close-quote;}
.profile-quote.editable > blockquote {
    display: none;
}
.profile-quote.editable > #profile-quote-edit-form {
    display: block !important;
}

.profile-info .media-body a {
    color: #e0dede;
    font-size: 15px;
}
.profile-info .media-body a > i {
    font-size: 12px;
}
.profile-info .media-body a:hover {color: #fff;}
.qr-code {
    text-align: center;
}
.qr-code img {
    background: #fff;
}
#profile-follow {
    position: absolute;
    top: 15px;
    right: 10px;
}
.profile-quote.editable form {
    background: #fafafb;
    border: 1px solid #e9e9e9;
}
#profile-quote-edit-form > .text > textarea {
    background: transparent;
    border: none;
    box-shadow: none;
}
.profile-quote.editable form > .button {
    padding: 5px 10px;
    background: #F6F7F9;
    border-top: 1px solid #e9e9e9;
}
.profile-quote.editable #profile-quote-edit-alert {
    display: none;
}
.profile-info .list-inline > li {
    position: relative;
    padding-right: 10px;
    color: #bbb;
}
.profile-info .list-inline > li::before {
    content: "";
    height: 14px;
    width: 1px;
    background: #6e6e6e;
    position: absolute;
    right: 0;
    top: 5px;
}
.following .list-inline > li > a,
.followers .list-inline > li > a {
    margin: 0 -10px;
    border-radius: 50%;
}
</style>
@endsection
