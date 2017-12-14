<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Helper;
use Cache;
use LRedis;
use App\Traits\Blogable;
use Carbon\Carbon;
use Input;

class Blog extends Model
{
    use Blogable;

    protected $fillable = ['id', 'user_id', 'title', 'slug', 'content', 'thumbnail', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class);
    }

    public function getCreatedAtAttribute($date)
    {
         return $this->get_created_at($date);
    }

    public static function fetchAll()
    {
        $expiresAt = Carbon::now()->addDays(7);
        $result = Cache::remember('blog_posts_cache', $expiresAt, function() {
            return self::with(array('user'=>function($query){
                $query->select('id','name','avatar', 'slug');
            }))->orderBy('created_at', 'desc')->get();
        });
        return $result;
    }

    static public function fetchPaginate()
    {
        // $expiresAt = Carbon::now()->addDays(7);
        // $currentPg = Input::get('page') ? Input::get('page') : '1';
        // $result = Cache::remember('page_cache_' . $currentPg, $expiresAt, function() {
            return self::with(array('user'=>function($query){
                $query->select('id','name','avatar', 'slug');
            }))->orderBy('created_at', 'desc')->paginate(5);
        // });
        // return $result;
    }

    public static function fetch($slug)
    {
        $data = Helper::extract_id_slug($slug);
        $expiresAt = Carbon::now()->addDays(7);
        $result = Cache::remember('post:' . $slug . '.html', $expiresAt, function() use($data) {
            return self::where('id', $data['id'])->where('slug', $data['slug'])->firstOrFail();
        });
        return $result;
    }

}
