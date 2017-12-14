@extends('layouts.app')
@section('class', 'detail')
@section('title', $post->title)
@section('content')
@include('layouts.blocks.__navbar')
<div class="container">
    <div class="maxw680 m0a">
        <div class="pt80 size16">
            <h1 class="title-detail">{{ $post->title }}</h1>
            @if(isset($post->user))
            <div class="media">
                <div class="media-left minw50">
                    <a href="{{ route('user.index', ['slug' => $post->user->slug]) }}" class="avatar-circle">
                        <img src="{{ url('image/48/48/' . $post->user->avatar) }}" width="48" class="img-circle avatar-xs">
                    </a>
                 </div>
                  <div class="media-body">
                      <h4 class="media-heading bold mb0">
                          <a href="{{ route('user.index', ['slug' => $post->user->slug]) }}">{{ $post->user->name }}</a>
                          @if($post->user->gender == 'male')
                            <i class="iconfont ic-man"></i>
                          @else
                            <i class="iconfont ic-woman"></i>
                          @endif
                      </h4>
                      <small>{{ Helper::get_created_at($post->created_at) }}</small>
                      @if(Auth::check() && Auth::user()->id === $post->user->id)
                        <small>
                            - <a href="{{ route('article.edit', ['slug' => $post->slug . '-' . $post->id]) }}" class="size12"><i class="iconfont ic-edit-s"></i> sưả</a>
                        </small>
                      @endif
                  </div>
            </div>
            @endif
            <hr>
            <div class="article-content">{!! $post->content !!}</div>

            <detail-meta-button></detail-meta-button>

            <div class="mb20"></div>
            <!-- AUTHOR DETAIL -->
            <div class="author-detail">
                <div class="info">
                    <a href="{{ route('user.index', ['slug' => $post->user->slug]) }}" class="pull-left">
                        <div class="avatar-circle">
                            <img src="{{ url('image/48/48/' . $post->user->avatar) }}" width="48" class="img-circle avatar-xs">
                        </div>
                    </a>
                    <follow :member_id="{{ $post->user->id }}" class="pull-right"></follow>
                    <a class="name" href="{{ route('user.index', ['slug' => $post->user->slug]) }}">
                        {{ $post->user->name }}
                        <div class="author_rank inline-block">
                            <ul class="list-inline">
                                <li class="relative" data-tooltip="Tác giả" data-placement="top" style="width:25px !important;">
                                    <img src="{{ asset('images/author.png') }}" width="18px">
                                </li>
                            </ul>
                        </div>
                    </a>
                    <p class="size12">{{ isset($post->articles_count) ? $post->articles_count : 0 }} bài viết - 8.681 lượt thích bài viết - {{ count($post->user->followers) }} người theo dõi</p>
                </div>
                <div class="signature">
                    {{ isset($post->user->profile->quote) ? $post->user->profile->quote : '' }}
                </div>
            </div>
            <!-- AUTHOR DETAIL -->
        </div>
    </div>
    <div class="comment-box row">
        <div class="maxw680 m0a">
            <!-- COMMENT BOX -->
            <comment-box :post_id="{{ $post->id }}"></comment-box>
            <!-- COMMENT BOX -->
        </div>
    </div>
    <div class="related_by_user ">
        <div class="maxw680 m0a">
            <h4 class="bold with-border-bottom text-uppercase"><i class="iconfont ic-discover-user"></i> Cùng tác giả</h4>
            @foreach(Helper::get_articles_by_user($post->user->id, 0, 5) as $article)
                @include('layouts.blocks.__article_content')
            @endforeach
        </div>
    </div>
</div>



<!-- NAVBAR DETAIL AND POST TITLE -->
<div class="navbar-detail">
    <div class="container">
        <div class="row">
            <div class="home">
                <a href="{{ url('/') }}" class="navbar-brand" title="Cốc Cốc">
                    <img src="{{ asset('images/svg/bird.svg') }}">
                </a>
            </div>
            <div class="navbar-article">
                <h4 class="navbar-article-title"><i class="iconfont ic-arrow"></i> {{ $post->title }}</h4>
                
                <single-like :post_id="{{ $post->id }}" class="pull-right"></single-like>
                
            </div>
        </div>
    </div>
</div>
<!-- NAVBAR DETAIL AND POST TITLE -->
@endsection
@section('style')
<style media="screen">
.navbar.navbar-default .container {position: relative;}
.comment-box {
    background: #f5f5f5;
}
</style>
@endsection
@section('script')
<script type="text/javascript">
$(document).on('scroll', function() {
    if($(this).scrollTop() >= ($('.title-detail').position().top + $('.title-detail').height())){
        $('.navbar-default').addClass('is-passive');
        $('.navbar-detail').addClass('is-active');
        $('.navbar-default').find('li.dropdown.open').removeClass('open');
    }else{
        $('.navbar-default').removeClass('is-passive');
        $('.navbar-detail').removeClass('is-active');
    }
})
</script>
@endsection
