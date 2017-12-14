@extends('layouts.app')
@section('title', 'Messenger')
@section('class', 'full-width messenger-page')
@section('content')
@include('layouts.blocks.__navbar')
<div class="wrapper pt60">
    @if(isset($chatter))
        <chat :chatting="{{ $chatter }}"></chat>
    @else
        <chat></chat>
    @endif
</div>
@endsection

@section('script')
<script type="text/javascript">
    // $('.chat[data-chat=person2]').addClass('active-chat');
    // $('.person[data-chat=person2]').addClass('active');

    // $('.left .person').mousedown(function(){
    //     if ($(this).hasClass('.active')) {
    //         return false;
    //     } else {
    //         var findChat = $(this).attr('data-chat');
    //         var personName = $(this).find('.name').text();
    //         $('.right .top .name').html(personName);
    //         $('.chat').removeClass('active-chat');
    //         $('.left .person').removeClass('active');
    //         $(this).addClass('active');
    //         $('.chat[data-chat = '+findChat+']').addClass('active-chat');
    //     }
    // });
    $('.wHeight').css('height', $(window).height());
    $(window).resize(function() {
        var winHeight = $(window).height();
        $('.wHeight').css('height', winHeight);
    });
    // $('.person').on('click', function() {
    //     var oldheight = $('#chat-main').height();
    //     $('#chat-main').css('height', $(window).height() - 162);
    //     var winHeight = $(window).height();
    //     $('.wHeight').css('height', winHeight);
    // });
</script>
@endsection

@section('style')

@endsection
