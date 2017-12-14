<nav class="navbar navbar-default navbar-fixed-top h60 with-border-bottom">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="36px" height="36px"
                	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <polygon style="fill:rgb(255, 210, 172)" points="317.231,191.842 331.337,220.885 185.35,246.256 167.244,377.653 201.745,397.069
                	376.224,313.3 397.966,262.71 417.714,251.591 421.774,207.305 "/>
                <polygon style="fill:rgb(227, 91, 38)" points="421.774,207.305 446.255,279.684 398.073,262.758 397.966,262.71 "/>
                <polygon style="fill:rgb(255, 155, 70)" points="317.231,191.842 376.224,313.3 185.35,246.256 65.745,0 256.62,67.044 "/>
                <polygon style="fill:rgb(255, 155, 70)" points="201.745,397.069 206.974,445.169 92.008,512 185.35,246.256 "/>
                </svg>
                CỐCCỐC<i>.ME</i>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle pr0i" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="iconfont ic-hot"></i> Trending <i class="iconfont ic-arrow rotate-90"></i></a>
                    </a>
                    <ul class="dropdown-menu with-arrow with-arrow-left">
                        <li><a href="{{ route('article.popular') }}">Xem nhiều nhất</a></li>
                        <li><a href="{{ route('article.trending_week') }}">Hot nhất tuần</a></li>
                        <li><a href="{{ route('article.trending_month') }}">Hot nhất tháng</a></li>
                    </ul>
                </li>
            </ul>


            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                @if(Auth::check())
                    <unread-notification :id="{{ Auth::user()->id }}"></unread-notification>
                    <unread-messages :id="{{ Auth::user()->id }}"></unread-messages>
                @endif
                <!-- Day Night button -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle navbar-more-button" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                        <img src="{{ asset('images/svg/three-dots-menu.svg') }}" class="rotate-90" alt="">
                    </a>
                    <ul class="dropdown-menu with-arrow with-arrow-center">
                        <li>
                            <a>
                                Chế độ Ngày - Đêm
                                <i class="toggle-box">
                                    <input type="checkbox" name="checkbox1" id="toggle-box-checkbox" />
                                    <label for="toggle-box-checkbox" class="toggle-box-label-left"></label>
                                    <label for="toggle-box-checkbox" class="toggle-box-label"></label>
                                </i>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('login') }}"><i class="iconfont ic-user"></i> Đăng nhập</a></li>
                    <li><a href="{{ route('register') }}">Đăng ký</a></li>
                @else
                    <li class="write_bottom">
                        <a href="{{ route('article.create') }}">
                            <i class="iconfont ic-write"></i> Viết
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle pr0i" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            <img src="{{ url('image/24/24/' . Auth::user()->avatar) }}" width="24" class="img-circle avatar-xs with-border">
                            {{ explode(' ', Auth::user()->name)[0] }} <i class="iconfont ic-arrow rotate-90"></i>
                        </a>

                        <ul class="dropdown-menu with-arrow">
                            <li class="with-border-bottom"><a href="{{ route('user.index', ['slug' => Auth::user()->slug]) }}"><i class="iconfont ic-settings-profile"></i> Trang cá nhân</a></li>
                            <li class="with-border-bottom"><a href="{{ url('/chat') }}"><i class="iconfont ic-comment"></i> Chat Messenger</a></li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    <i class="iconfont ic-navigation-signout"></i> Thoát
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
