@extends('layouts.app')

@section('content')
@include('layouts.blocks.__navbar')
<div class="container">
    <div class="maxw680 m0a">
        <div class="pt80 size16">
            <h1>{{ $post->title }}</h1>
            <div class="media">
                <div class="media-left minw50">
                    <div class="avatar-circle">
                        <img src="{{ url('image/48/48/' . $post->user->avatar) }}" width="48" class="img-circle avatar-xs">
                    </div>
                 </div>
                  <div class="media-body">
                      <h4 class="media-heading bold">
                          {{ $post->user->name }}
                          @if($post->user->gender == 'male')
                            <i class="iconfont ic-man"></i>
                          @else
                            <i class="iconfont ic-woman"></i>
                          @endif
                          <small><a href="{{ route('user.index', ['slug' => $post->user->slug]) }}">{{ '@'.$post->user->slug }}</a></small>
                      </h4>
                      <small>{{ $post->created_at }}</small>
                      @if(Auth::check() && Auth::user()->id === $post->user->id)
                        <a href="{{ route('blog.edit', ['slug' => $post->slug . '-' . $post->id]) }}">Edit</a>
                      @endif
                  </div>
            </div>
            <span>{!! $post->content !!}</span>
        </div>
    </div>
</div>
@endsection
@section('style')
<style media="screen">

</style>
@endsection
