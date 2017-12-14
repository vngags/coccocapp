<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('images/svg/bird.svg') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Cốc Cốc</title>
    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/fontawesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/swal/sweetalert2.css') }}">
    @yield('style')
    <!-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> -->
</head>
<body id="body" class="white-page @yield('class')" spellcheck="false">
    <div id="app">
        
        @yield('content')
        @if(Auth::check())
            <init></init>
            <article-quick-view></article-quick-view>
            <chatbox></chatbox>
        @endif        
    </div>
    @include('layouts.blocks.__footer')
    <!-- Scripts -->
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/app.front.js') }}"></script>
    @yield('script')
</body>
</html>
