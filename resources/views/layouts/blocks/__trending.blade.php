<div class="trending">
    <a class="weekly" href="{{ route('article.trending_week') }}">
        HOT nhất tuần
        <!-- <i class="fa fa-calendar-check-o"></i> -->
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="50px" height="50px" viewBox="0 0 16 16">
            <path fill="#ABD8CF" d="M4.9 15.8c0 0-3.9-0.4-3.9-5.7 0-4.1 3.1-6.5 3.1-6.5s1.3 1.4 2.3 1.9c1 0.6 1.4-5.5 1.4-5.5s7.2 3.9 7.2 9.8c0 6.1-4 5.9-4 5.9s1.8-2.4 1.8-5.2c0-3-3.9-6.7-3.9-6.7s-0.5 4.4-2.1 5c-1.6-0.9-2.5-2.3-2.5-2.3s-3.7 5.8 0.6 9.3z"/>
            <path fill="#ABD8CF" d="M8.2 16.1c-2-0.1-3.7-1.4-3.7-3.2s0.7-2.6 0.7-2.6 0.5 1 1.1 1.5 1.8 0.8 2.4 0.1c0.6-0.6 0.8-2.3 0.8-2.3s1.4 1.1 1.2 3c-0.1 2-0.9 3.5-2.5 3.5z"/>
        </svg>
    </a>
    <a class="monthly" href="{{ route('article.trending_month') }}">
        HOT nhất tháng
        <i class="iconfont ic-hot first"></i>
        <i class="iconfont ic-hot seconds"></i>
    </a>
    <a class="most_view" href="{{ route('article.popular') }}">
        Xem nhiều nhất
        <i class="iconfont ic-settings-verify"></i>
    </a>
    <a class="most_like" href="{{ route('article.popular') }}">
        Yêu thích nhất
        <i class="iconfont ic-article-like"></i>
    </a>
</div>
