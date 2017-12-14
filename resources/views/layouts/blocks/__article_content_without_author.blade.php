<div class="article-list-item {{ $post->thumbnail ? 'has-img' : 'no-img' }}">
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
