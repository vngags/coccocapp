@extends('layouts.app')
@section('title', 'Đăng ký tài khoản')
@section('content')
<div class="pt80">
    <div class="container">
        <div class="row">
            <div class="maxw400 m0a">
                <div class="panel white-bg">
                    <div class="panel-body p40">
                        <ul class="list-inline ml0 text-center text-uppercase bold">
                            <li class="list-inline-item"><h5><a href="{{ route('login') }}"><i class="iconfont ic-user"></i> Đăng nhập</a></h5></li>
                            <li class="list-inline-item active"><h5><i class="iconfont ic-password"></i> Đăng ký</h5></li>
                        </ul>
                        <hr>
                        <form method="POST" action="{{ route('register') }}">
                            {{ csrf_field() }}

                            <div class="input-prepend first relative form-group">
                                    <div class="form-control">
                                        <label class="control-label">Giới tính</label>
                                        <div class="radio-inline">
                                            <input type="radio" class="genderId" checked name="gender" id="genderId2RadioMale" value="male">
                                            <label for="genderId2RadioMale"><i class="iconfont ic-man"></i> Nam</label>
                                        </div>
                                        <div class="radio-inline">
                                            <input type="radio" class="genderId" name="gender" id="genderId2RadioFemale" value="female">
                                            <label for="genderId2RadioFemale"><i class="iconfont ic-woman"></i> Nữ</label>
                                        </div>
                                    </div>
                                    <i class="fa fa-venus-mars" aria-hidden="true"></i>
                            </div>

                            <div class="input-prepend no-border-bottom relative form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Tên hiển thị" required autofocus>
                                    <i class="iconfont ic-user"></i>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="input-prepend no-border-bottom relative form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Thư điện tử" required>
                                    <i class="iconfont ic-email"></i>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="input-prepend no-border-bottom relative form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
                                    <i class="iconfont ic-password"></i>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="input-prepend last relative form-group mb20">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                                    <i class="iconfont ic-password"></i>
                            </div>

                            <div class="form-group">
                                    <button type="submit" class="btn button-success btn-lg btn-block">
                                        Register
                                    </button>
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
    .radio-inline i {
        font-weight: bold;
        font-size: 16px;
    }
    #chooseGender {
        text-align: right;
        padding: 10px 20px 5px;
        border-top: 1px solid #ccc;
        font-size: 12px;
        background: #f6f7f9;
        border-radius: 0 0 4px 4px;
    }
    .radio-inline label {
        font-weight: normal;
    }
</style>
@endsection
