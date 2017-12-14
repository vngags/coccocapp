function backtotop() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

$(window).scroll(function () {
    var $this = $(this);
    if ($this.scrollTop() > 500) {
       $('.scrollTop').fadeIn();
    } else {
       $('.scrollTop').fadeOut();
    }
});

$("img").not('.lazyloaded').lazyload({
    effect : "fadeIn",
    effectTime: 2000,
    threshold: 150
}).addClass('lazyloaded');

//Loadmore Posts
function fetchPosts() {
    var start = $('#articles-container').attr('start');
    var total = $('#articles-container').attr('total');
    if(parseInt(start) > 0 && parseInt(start) < parseInt(total)) {
        clearTimeout($.data(this, "scrollCheck"));
        $.data(this, "scrollCheck", setTimeout(function() {
            var bottom_page = $(window).height() + $(window).scrollTop() + 100;
            if(bottom_page >= $(document).height()) {
                $('.form-group.loading').css('display', 'block');
                $.get('?start='+start, function(data) {
                    $('#articles-container').append(data.posts);
                    $('#articles-container').attr('start', data.cursor);
                    $('.form-group.loading').css('display', 'none');
                    $("img").not('.lazyloaded').lazyload({
                        effect : "fadeIn",
                        effectTime: 2000,
                        threshold: 150
                    }).addClass('lazyloaded');
                });
            }
        }, 500))
    }
}
// function fetchPosts() {
//     var next_page = $('#articles-container').attr('nextpage');
//     var hasmore_page = $('#articles-container').attr('hasmore');
//     if(next_page != null && hasmore_page == 1) {
//         clearTimeout($.data(this, "scrollCheck"));
//         $.data(this, "scrollCheck", setTimeout(function() {
//             var bottom_page = $(window).height() + $(window).scrollTop() + 100;
//             if(bottom_page >= $(document).height()) {
//                 $('.form-group.loading').css('display', 'block');
//                 $.get(next_page, function(data) {
//                     $('#articles-container').append(data.posts);
//                     $('#articles-container').attr('nextpage', data.next_page);
//                     $('#articles-container').attr('page', data.current_page);
//                     $('#articles-container').attr('hasmore', data.hasmore_page);
//                     $('.form-group.loading').css('display', 'none');
//                     //Uncomment nếu muốn mỗi khi load dữ liệu xong sẽ tự động scroll đến item đầu tiên vừa load
//                     //$('html, body').animate({ scrollTop: $('#article-'+data.first_item).offset().top - 60 }, 'slow');
//                 });
//             }
//         }, 500))
//     }
// }
//\Loadmore posts

//rê chuột hiện thông tin author ngoài trang chủ
// var get_author_info;
// $(document).on({
//     mouseenter: function() {
//         var user_dropdown = $(this).find('[data_render_container]');
//         var user_slug = $(user_dropdown).attr('data-user');
//         // get_author_info = window.setTimeout(function() {
//         //     $(user_dropdown).find('[data_render_main]').html('<i class="fa fa-spinner fa-spin"></i>');
//         //     $(user_dropdown).show().animate({opacity:1},1000, function(){                
//         //         $.get(`/u/${user_slug}`, function(data) {
//         //             $(user_dropdown).find('[data_render_main]').html(data.user);
//         //         });
//         //     });
//         // }, 750)
//         $(user_dropdown).delay(500).fadeTo(500, 1).animate({opacity:1},500, function(){
//             $.get(`/u/${user_slug}`, function(data) {
//                 $(user_dropdown).find('[data_render_main]').html(data.user);
//             });
//         });
//     },
//     mouseleave: function() {
//         $('[data_render_container]').delay(750).fadeOut();
//         // $('[data_render_container]').stop(true, true).css("display", "none");
//         setTimeout(function() {            
//             // $('[data_render_main]').html('');
//              window.clearTimeout(get_author_info);
//         }, 1000);
//     }
// }, '.author_hover');
//Kết thúc phần rê chuột

