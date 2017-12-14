<div class="article-list-item {{ $post->thumbnail ? 'has-img' : 'no-img' }}">
    <div class="author mb10 relative">
        <div class="author_hover">
            <div class="avatar pull-left">
                <a href="{{ route('user.index', ['slug' => $post->user->slug]) }}" class="avatar-circle">
                    <img src="/image/32/32/{{ $post->user->avatar }}" class="img-circle" width="24">
                </a>
            </div>
            <div class="info ml30 lh24 relative">
                <div class="author_info">
                    <a href="{{ route('user.index', ['slug' => $post->user->slug]) }}" class="author_name">
                        {{ $post->user->name }}
                    </a>                    
                </div>
            </div>
            <!-- AUTHOR INFO POPUP -->
            <div class="user_info_dropdown">
                <span class="top-transparent"></span>
                <div class="user_info_content text-center">
                    <div class="user_info_header relative">
                        <div class="profile-cover" style="background-image:url({{ url('image/96/96/'. $post->user->avatar) }})"></div>
                        <div class="avatar-circle">
                            <img src="/image/64/64/{{ $post->user->avatar }}" class="img-circle avatar-outline">
                        </div>
                        <p class="bold">{{ $post->user->name }}</p>
                    </div>
                    <div class="form-group p10">
                        <p>{{ $post->articles_count }} bài viết, {{ count($post->user->followers) }} người đang theo dõi, đang theo dõi {{ count($post->user->followings) }} người</p>
                    </div>
                    <div class="user_info_middle">
                        <p>{{ isset($post->user->profile->quote) ? $post->user->profile->quote : '' }}</p>
                    </div>
                    <div class="user_info_footer minw200 clearfix">
                        <follow :member_id="{{ $post->user->id }}" class="pull-right" id="profile-follow"></follow>
                    </div>                                    
                </div>
            </div>
            <!-- AUTHOR INFO POPUP -->
        </div>

        <small class="text-brown">{{ Helper::get_created_at($post->created_at) }}</small>
        @if(Auth::check() && Auth::user()->id === $post->user->id)
        <div class="author_action">
            <ul class="absolute">
               <li class="dropdown">
                  <img src="{{ asset('images/svg/three-dots-menu.svg') }}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle">
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li>
                         <a class="boldi" href="{{ route('article.edit', ['slug' => $post->slug . '-' . $post->id]) }}">
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
                <a href="{{ Helper::get_permalink($post->slug, $post->id) }}" title="{{ $post->title }}">{{ $post->title }}</a>
            </h2>
            <div class="article-list-summary two_lines">
                {{ html_entity_decode(strip_tags(substr($post->content, 0, strpos($post->content, "</p>")))) }}
            </div>
        </div>
        @if($post->thumbnail)
            <a href="{{ Helper::get_permalink($post->slug, $post->id) }}" class="wrap-img">
                <img class="lazyload" data-original="{{ url('image/150/120/' . $post->thumbnail) }}" src="{{ asset('images/thumbnail-holder.png') }}" alt="{{ $post->title }}">
            </a>
        @endif
    </div>
    <div class="article-list-meta">
        <ul class="list-inline">
            <li><i class="iconfont ic-list-read"></i>{{ $post->views }}</li>
            <li><i class="fa fa-comments-o"></i>{{ $post->comments }}</li>
            <li><i class="iconfont ic-list-like"></i>{{ mt_rand(1, 100) }}</li>
        </ul>
    </div>
</div>
