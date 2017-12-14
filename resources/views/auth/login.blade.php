@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')
<div class="pt80">
    <div class="container">
        <div class="row">
            <div class="maxw400 m0a">
                <div class="panel white-bg">
                    <div class="panel-body p40">
                        <ul class="list-inline ml0 text-center text-uppercase bold">
                            <li class="list-inline-item active"><h5><i class="iconfont ic-user"></i> Đăng nhập</h5></li>
                            <li class="list-inline-item"><h5><a href="{{ route('register') }}"><i class="iconfont ic-password"></i> Đăng ký</a></h5></li>
                        </ul>
                        <hr>
                        <form method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="input-prepend relative first form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input id="email" type="email" class="form-control" name="email" placeholder="Thư điện tử" value="{{ old('email') }}" required autofocus>
                                <i class="iconfont ic-email"></i>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="input-prepend relative last form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input id="password" type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
                                <i class="iconfont ic-password"></i>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt10">
                                <div class="checkbox-group">
                                    <label>
                                       <input type="checkbox" class="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                       <i class="checkbox-icon"></i>
                                       Ghi nhớ đăng nhập
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn button-primary btn-lg btn-block">
                                    Đăng nhập
                                </button>
                                <p class="text-center">
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        Quên mật khẩu?
                                    </a>
                                </p>
                            </div>
                        </form>
                        <hr class="w200 m0a mb20">
                        <div class="form-group">
                            <ul class="list-inline ml0 text-center">
                                <li><img src="{{ asset('images/svg/Facebook.svg') }}" class="img-circle" width="50"></li>
                                <li><img src="{{ asset('images/svg/Twitter.svg') }}" class="img-circle" width="50"></li>
                                <li><img src="{{ asset('images/svg/Google.svg') }}" class="img-circle" width="50"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('style')
<style media="screen">
    .white-page {
        background: #f1f1f1 !important;
    }
</style>
@endsection
