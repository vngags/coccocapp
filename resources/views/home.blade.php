@extends('layouts.app')

@if(isset($trending) && $trending == 'week')
    @section('title', 'HOT nhất tuần')
@elseif(isset($trending) && $trending == 'month')
    @section('title', 'HOT nhất tháng')
@elseif(isset($trending) && $trending == 'most_view')
    @section('title', 'Xem nhiều nhất')
@else
    @section('title', 'Trang chủ')
@endif

@section('content')
@include('layouts.blocks.__navbar')
<div class="container pt80">
    <div class="row">
        <div class="col-md-16 pr0i">
            <div class="panel no-shadow pb100">
                @if(Auth::check())
                    <!-- <div class="alert alert-info">
                        Chào mừng <strong>{{ Auth::user()->name }}</strong> đã quay trở lại với Cốc Cốc
                    </div> -->
                @endif
                <div class="trending_banner">
                    @if(isset($trending) && $trending == 'week')
                    <p class="week"><i class="iconfont ic-hot"></i> HOT nhất tuần <small class="pull-right text-brown normali size12" style="text-transform:capitalize;line-height:34px;display:block;">(Tìm thấy {{ $total }} bài viết)</small></p>
                    @endif
                    @if(isset($trending) && $trending == 'month')
                    <p class="month"><i class="iconfont ic-hot"></i> HOT nhất tháng <small class="pull-right text-brown normali size12" style="text-transform:capitalize;line-height:34px;display:block;">(Tìm thấy {{ $total }} bài viết)</small></p>
                    @endif
                    @if(isset($trending) && $trending == 'most_view')
                    <p class="most_view"><i class="fa fa-eye"></i> Xem nhiều nhất <small class="pull-right text-brown normali size12" style="text-transform:capitalize;line-height:34px;display:block;">(Tìm thấy {{ $total }} bài viết)</small></p>
                    @endif
                </div>
                <div id="articles-container" class="form-group" start="{{ $cursor }}" total="{{ $total }}">
                    @foreach($posts as $post)
                        @include('layouts.blocks.__article_content_loop')
                    @endforeach
                </div>
                <div class="form-group loading" style="display:none">
                    <!-- LOADING -->
                    <div class="notes-placeholder index">
                        <div class="img"></div>
                        <div class="content">
                            <div class="author">
                                <div class="avatar"></div>
                                <div class="name"></div>
                            </div>
                            <div class="title"></div>
                            <div class="text"></div>
                            <div class="text animation-delay"></div>
                            <div class="meta">
                                <div class="tag"></div>
                                <i class="iconfont ic-list-read"></i>
                                <div class="read"></div>
                                <i class="fa fa-comments-o"></i>
                                <div class="small"></div>
                                <i class="iconfont ic-list-like"></i>
                                <div class="small"></div>
                            </div>
                        </div>
                    </div>
                    <!-- LOADING -->
                </div>
            </div>
        </div>
        <div class="col-md-7 col-md-offset-1 right-column">
            @include('layouts.blocks.__trending')
            <hr>
            @include('layouts.blocks.__top_author')
            <div class="sidebar-footer">
                @if(count($users_online) > 0)
                {{ count($users_online) }} thành viên đang trực tuyến
                <p>
                    @foreach($users_online as $u)
                        {{ $u['name'] }}
                    @endforeach
                </p>
                @endif
            </div>
            <hr>
            <p>Hôm nay là: {{ $dayofweek }} {{ $today }} tức ngày: {{ $lunar }} âm lịch</p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(window).scroll(fetchPosts);
</script>
@endsection

@section('style')
<style media="screen">

</style>
@endsection
