1 - Sử dụng Controller ArticleController

2 - Các hàm trong Article Model
    fetchAll() : Get toàn bộ dữ liệu
    fetch($slug): Get dữ liệu 1 bài viết, slug có dạng: tieu-de-bai-viet-1
    fetchPaginate(): Get dữ liệu theo từng trang
    _store($data): Lưu dữ liệu lên server: ($data: request->all)
                Trong  hàm này ta gọi đến self::__store($data) trong traits Blogable.php
                __store sẽ gọi đến update_post nếu dữ liệu trong $data tồn tại (cập nhật)
                hoặc create_post nếu chưa tồn tại
    get_redis_by_key($key): Trả về dữ liệu trong redis theo key nếu tồn tại
    redis_key_exists($key): Trả về 1 nếu key tồn tại, 0 nếu không tồn tại

articleView: ZRANGE

Hệ thống comment REDIS
hash key: comments
        field: comment:<post_id>:<comment_id>:<user_id> - sử dung hscan để lấy theo regex
hash key: child-comments
        field: comment:<comment_id>:<user_id> - Sử dụng hscan để lấy

List Póst by user_id:
    ZADD KEY <INT SCORE> <STRING MEMBER>
    ZADD('articles_by_user:<user_id>', 'article_<post_id>'); ex: articles:1 => article_1
    _ZRANGE KEY 0 -1 to return all value
    ZCARD to count
    ZSCAN to get paginate

Luu post vao redis theo ngay khoi tao:
    $last_7_days = Carbon::today()->subDays(1);
    $today = Carbon::now();
    $posts = LRedis::ZRANGEBYSCORE('articles_by_date', strtotime($last_7_days), strtotime($today));//ZRANGEBYSCORE('keys', 'start', 'end')
    return $posts;

Set Top Members
    ZADD ('top_members', '<int count posts>', 'user:<id>')


Hệ thống follow:
    SADD('following:<id>', <user_id>)
    SREM('following:<id>', <user_id>) -> remove user_id from key

Like Post :
    SADD('article_likes:<post_id>', <user_id>)
    SADD('user_likes:<user_id>', <article<id>)


install laravel-echo npm install --save socket.io express laravel-echo-server
    "dotenv": "^2.0.0",
    "laravel-echo": "^1.3.0",
    "laravel-echo-server": "^1.1.0",
    "redis": "^2.4.2",
    "socket.io": "^1.4.5",


_npm install --save vue-timeago

Messenger:
    Lưu List của từng cặp chát dạng private_messages:<user_id1>:<user_id2>
    Them message vao list: RPUSH(key, value(message, from_code, time))
    Khi vào trang chát của thành viên sẽ lấy private_messages:<id>:<user_id>

npm install --save vue-chat-scroll

npm install --save vue-animated-list