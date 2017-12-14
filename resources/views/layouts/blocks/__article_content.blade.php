<div class="article-list-item {{ $article->thumbnail ? 'has-img' : 'no-img' }}">
    <div class="author mb10 relative">
        <div class="author_hover">
            <div class="avatar pull-left">
                <a href="{{ route('user.index', ['slug' => $article->user->slug]) }}" class="avatar-circle">
                    <img src="/image/32/32/{{ $article->user->avatar }}" class="img-circle" width="24">
                </a>
            </div>
            <div class="info ml30 lh24 relative">
                <div class="author_info">
                    <a href="{{ route('user.index', ['slug' => $article->user->slug]) }}" class="author_name">
                        {{ $article->user->name }}
                    </a>                    
                </div>
            </div>
            <!-- AUTHOR INFO POPUP -->
            <div class="user_info_dropdown">
                <span class="top-transparent"></span>
                <div class="user_info_content text-center">
                    <div class="user_info_header relative">
                        <div class="profile-cover" style="background-image:url({{ url('image/96/96/'. $article->user->avatar) }})"></div>
                        <div class="avatar-circle">
                            <img src="/image/64/64/{{ $article->user->avatar }}" class="img-circle avatar-outline">
                        </div>
                        <p class="bold">{{ $article->user->name }}</p>
                    </div>
                    <div class="form-group">
                        <p>{{ $article->articles_count }} bài viết, {{ count($article->user->followers) }} người đang theo dõi, đang theo dõi {{ count($article->user->followings) }} người</p>
                    </div>
                    <div class="user_info_middle">
                        <p>{{ isset($article->user->profile->quote) ? $article->user->profile->quote : '' }}</p>
                    </div>
                    @if(Auth::check() && Auth::user()->id !== $article->user->id)
                        <div class="user_info_footer minw200">
                            <follow :member_id="{{ $article->user->id }}" class="pull-right" id="profile-follow"></follow>
                        </div>
                    @endif                                                    
                </div>
            </div>
            <!-- AUTHOR INFO POPUP -->
        </div>

        <small class="text-brown">{{ Helper::get_created_at($article->created_at) }}</small>
        @if(Auth::check() && Auth::user()->id === $article->user->id)
        <div class="author_action">
            <ul class="absolute">
               <li class="dropdown">
                  <img src="{{ asset('images/svg/three-dots-menu.svg') }}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle">
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li>
                         <a class="boldi" href="{{ route('article.edit', ['slug' => $article->slug . '-' . $article->id]) }}">
                             Chỉnh sửa
                             <small class="normali text-brown block">Chỉnh sửa bài viết</small>
                        </a>
                     </li>
                  </ul>
               </li>
            </ul>
        </div>
        @endif
    </div>

    <div class="article-list-body relative">
        <div class="article-list-content">
            <h2 class="article-list-title two_lines mt0">
                <a href="{{ Helper::get_permalink($article->slug, $article->id) }}" title="{{ $article->title }}">{{ $article->title }}</a>
            </h2>
            <div class="article-list-summary two_lines">
                {{ html_entity_decode(strip_tags(substr($article->content, 0, strpos($article->content, "</p>")))) }}
            </div>
        </div>
        @if($article->thumbnail)
            <a href="{{ Helper::get_permalink($article->slug, $article->id) }}" class="wrap-img">
                <img class="lazyload" data-original="{{ url('image/150/120/' . $article->thumbnail) }}" src="{{ asset('images/thumbnail-holder.png') }}" alt="{{ $article->title }}">
            </a>
        @endif
    </div>
    <div class="article-list-meta">
        <ul class="list-inline">
            <li><i class="iconfont ic-list-read"></i>{{ $article->views }}</li>
            <li><i class="fa fa-comments-o"></i>{{ $article->comments }}</li>
            <li><i class="iconfont ic-list-like"></i>{{ mt_rand(1, 100) }}</li>
        </ul>
    </div>
</div>
