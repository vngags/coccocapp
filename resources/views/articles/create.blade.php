@extends('layouts.app')
@section('title', 'Chia sẻ câu chuyện của bạn')
@section('content')
@include('layouts.blocks.__navbar')
<div class="container">
    <div class="pt80">
        <div class="main m0a">
            <div class="form-group">
                <h3 class="write-title-message text-uppercase text-center">Chia sẻ câu chuyện của bạn</h3>
            </div>
            <hr>
        </div>
        <blog-form></blog-form>
    </div>
</div>
@endsection
