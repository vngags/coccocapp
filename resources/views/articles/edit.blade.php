@extends('layouts.app')
@section('title', 'Chỉnh sửa bài viết')
@section('content')
@include('layouts.blocks.__navbar')
<div class="container">
    <div class="pt80">
        <div class="main m0a">
            <div class="form-group">
                <h3 class="write-title-message text-uppercase text-center">Chia sẻ câu chuyện của bạn</h3>
            </div>
            <hr class="m0a w200 mt20 mb20">
        </div>
        <blog-form :slug=`{{ $slug }}` :draft="{{ $draft }}"></blog-form>
    </div>
</div>
@endsection
