<?php
namespace App\Helper;

use Illuminate\Support\Facades\Redis;
use App\User;
use Image;
use File;
use LRedis;
use App\Article;

class Helper
{
    static public function users_logged($cursor = null, $allResult = array())
    {
        if($cursor === "0") {
            $allResult = array_unique($allResult);
            $user = array();
            foreach ($allResult as $value) {
                $user[] = User::select('id', 'name', 'email')->where('id', Redis::GET($value))->first();
            }
            $user = array_unique($user);
            return $user;
        }

        if($cursor === null) {
            $cursor = "0";
        }
        $result = Redis::SCAN($cursor, 'match', 'users:*');
        $allResult = array_merge($allResult, $result[1]);
        return self::users_logged($result[0], $allResult);
    }

    static public function create_avatar_by_name($name)
    {
        $letter = strtoupper(substr($name, 0, 1));
        $color = array(
            '#0095ff', '#00b1b3', '#E91E63', '#f44336', '#2196F3', '#00bcd4', '#4caf50', '#cddc39', '#ff9800', '#9c27b0', '#673ab7'
        );
        $filename = str_slug($name) . '_' . str_random(6);
        $avatar = Image::canvas(200, 200, $color[array_rand($color)]);
        $avatar->text($letter, 100, 55, function($font) {
            $font->file(public_path('/fonts/arialbd.ttf'));
            $font->size(120);
            $font->color('rgba(255,255,255,1)');
            $font->align('center');
            $font->valign('top');
        });
        $avatar->save(public_path('images/upload_avatars/'.$filename.'.png'));
        return 'upload_avatars/' . $filename . '.png';
    }

    public static function extract_id_slug($slug)
    {
        $arr = array();
        $parts = explode('-',$slug);
        $arr['id'] = array_pop($parts);
        $arr['slug'] = implode('-', $parts);
        return $arr;
    }

    static public function get_permalink($slug, $id)
    {
        return route('article.show', ['slug' => $slug . '-' . $id]);
    }

    static public function timeago_en_vi($str)
    {
       $str = preg_replace("/(seconds|second)/", 'giây', $str);
       $str = preg_replace("/(minutes|minute)/", 'phút', $str);
       $str = preg_replace("/(hours|hour)/", 'giờ', $str);
       $str = preg_replace("/(days|day)/", 'ngày', $str);
       $str = preg_replace("/(weeks|week)/", 'tuần', $str);
       $str = preg_replace("/(months|month)/", 'tháng', $str);
       $str = preg_replace("/(years|year)/", 'năm', $str);
       $str = preg_replace("/(ago)/", 'trước', $str);
       return $str;
    }

    static public function get_created_at($date)
    {
        $timeago = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $time = date('h:i', strtotime($date));
        $date_time = date('Y-m-d', strtotime($date));
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', time()-86400);
        if($date_time == $today) {
            return self::timeago_en_vi($timeago);
        }elseif($date_time == $yesterday) {
            return 'Hôm qua lúc ' . $time;
        }else{
            if($year == date("Y")) {
             $res = $day . ' tháng ' . $month . ' lúc ' . $time;
             return $res;
            }
            $resp = $day . ' tháng ' . $month . ' năm ' . $year . ' lúc ' . $time;
            return $resp;
        }
    }

    static public function check_image_is_base64($url) {
       if(preg_match("/^data:image\/[^;]+;base64,/i", $url)){
          return true;
       }else{
          return false;
       }
    }



     /**
     * @param: file
     * @param: type
     * @param: Directory
     * @param: filename
     * @param: watermark member name
     */
     static public function save_attachment($file, $type, $dir, $filename = null, $user_slug = null)
     {
         if($filename == null) {
             $filename = $user_slug . mt_rand(11111111, 99999999). '_' . mt_rand(11111111, 99999999);
         }
        //WATERMARK
        // $text = '©' . $user_slug . ' - coccoc.me';
        // $font_size = 28;
        // $size = intval(ceil($font_size * 0.75));
        // $font_file = public_path('/fonts/coccoc.ttf');
        // $box = imagettfbbox($size, 0, $font_file, $text);
        // $box['width'] = intval(abs($box[4] - $box[0] - ($box[4] * 0.228)));
        // $box['height'] = intval(abs($box[5] - $box[1]));
        // $watermark = Image::canvas($box['width'], $box['height'], 'rgba(0,0,0,0.02)');
        // for( $x = -1; $x <= 1; $x++ ) {
        //     for( $y = -1; $y <= 1; $y++ ) {
        //         $watermark->text($text, 3 + $x, 3 + $y, function($font) use($size) {
        //             $font->file(public_path('/fonts/coccoc.ttf'));
        //             $font->size($size);
        //             $font->color('rgba(0,0,0,0.1)'); // Glow color
        //             $font->align('left');
        //             $font->valign('top');
        //         });
        //     }
        // }
        // $watermark->text($text, 3, 3, function($font) use($size) {
        //     $font->file(public_path('/fonts/coccoc.ttf'));
        //     $font->size($size);
        //     $font->color('rgba(255,255,255,0.8)');
        //     $font->align('left');
        //     $font->valign('top');
        // });
        $watermark = Image::canvas(86, 17, 'rgba(0,0,0,0.02)');
        $watermark->text('COCCOC.ME', 4, 4, function($font) {
            $font->file(public_path('/fonts/logo.ttf'));
            $font->size(12);
            $font->color('rgba(0,0,0,0.15)');
            $font->align('left');
            $font->valign('top');
        });
        $watermark->text('COCCOC.ME', 3, 3, function($font) {
            $font->file(public_path('/fonts/logo.ttf'));
            $font->size(12);
            $font->color('rgba(255,255,255,0.9)');
            $font->align('left');
            $font->valign('top');
        });
        $watermark->rotate(90);
        //\WATERMARK
        // CHECK directory
        if (!file_exists(public_path('images/' . $dir))) {
            mkdir(public_path('images/' . $dir), 0777, true);
        }
        // Check directory
        $image = '';
        if($type == 'file') {
            $ext = $file->extension();
            if ($ext == 'gif') {
               //Nếu là ảnh gif thì copy file
                copy($file->getRealPath(), public_path('images/'.$dir.'/'.$filename.'.'.$ext));
                $image = $dir.'/'.$filename.'.'.$ext;
            }else {
                  $img = Image::make($file);
                  $w = $img->width();
                  $h = $img->height();
                  //resize if width > 800
                  if($w > 1200) {
                     $img->resize(1200, null, function($constraint) {
                        $constraint->aspectRatio();
                     });
                  }
                  $img->insert($watermark, 'bottom-right', 0, round($h/2 - 43));
                  $img->save(public_path('images/'.$dir.'/'.$filename.'.'.$ext));
                  $image = $dir.'/'.$filename.'.'.$ext;
            }
        }elseif($type == 'avatar') {
            if(self::check_image_is_base64($file) == true) {
               Image::make($file)
                    ->save(public_path('images/'.$dir.'/'.$filename.'.png'));
               $image = $dir.'/'.$filename.'.png';
            }
        }
        return $image;
    }

    static public function get_thumbnail_by_image($url, $size) {
           $path_parts = pathinfo($url);
           $dir = $path_parts['dirname'];
           $ext = $path_parts['extension'];
           $filename = $path_parts['filename'];
           $image = array();
           if($ext == 'gif') {
              $sm = $dir . '/' . $filename . '.' . $ext;
              $image[$size] = $sm;
              $image['full'] = $sm;
           }else{
              $sm = $dir . '/' . $filename . '_' . $size . '.' . $ext;
              $full = $dir . '/' . $filename . '.' . $ext;
              if(File::exists('images/'.$sm)) {
                  $image[$size] = $sm;
              }
              $image['full'] = $full;
           }
           return $image;
    }

    static public function full2small($url, $size) {
           $path_parts = pathinfo($url);
           $dir = $path_parts['dirname'];
           $ext = $path_parts['extension'];
           $filename = $path_parts['filename'];
           $image = array();
           if($ext == 'gif') {
              $sm = $dir . '/' . $filename . '.' . $ext;
              $image[$size] = $sm;
           }else{
              $sm = $dir . '/' . $filename . '_' . $size . '.' . $ext;
              if(File::exists('images/'.$sm)) {
                  $image[$size] = $sm;
              }else {
                  $image[$size] = $dir . '/' . $filename . '.' . $ext;
              }
           }
           return $image;
    }

    static public function save_image_version($w, $h, $src)
    {
        $img_path = public_path('images/'.$src);
        if(File::exists($img_path)) {
            $fname = basename($img_path); // return name and ext
            $name = str_replace('_' . $w . '_' . $h, '', $fname);
            //Get member directory Eg: 1
            $dirname =pathinfo($img_path, PATHINFO_DIRNAME); //return http://coccoc.me/images/1
            $parts = explode('/',$dirname);//create array
            $directory = array_pop($parts); //return Eg: 1
            $filename = $directory . '/' . $name;
            //get sm url
            $sm_name = explode('.',$name);//return filename,jpg
            if($sm_name[1] == 'gif') {
                return $src;
            }
            $sm = $directory . '/' . $sm_name[0] . '_' . $w . '_' . $h . '.' . $sm_name[1];
            if(!File::exists($sm)) {
                $i = Image::make($img_path);
                if($w == 'full' && $h == 'full') {
                    return redirect('images/'. $src);
                }
                if($h == 'auto') {
                    $i->resize($w, null, function($constraint) {
                       $constraint->aspectRatio();
                    });
                    $sm = $directory . '/' . $sm_name[0] . '_' . $w . '_' . $h . '.' . $sm_name[1];
                    $i->save(public_path('images/'.$sm));
                    return $sm;
                }
                //resize full ratio
                $size_w = $i->width();
                $size_h = $i->height();
                $ratio1 = $size_h/$size_w;
                //Dau tien tinh xem hinh can cat la doc hay ngang
                $ratio2 = $h/$w;
                if($ratio1 <= $ratio2) {//Truong hop hinh goc rong chieu ngang hon hinh muon cat
                    // //ta lay theo chieu dai
                    if($size_h <= $h) {
                        $i->resize(null, $size_h, function($constraint) {
                           $constraint->aspectRatio();
                        })
                        ->resizeCanvas(round($size_h/$ratio2), $size_h);
                    }else{
                        $i->resize(null, $h, function($constraint) {
                           $constraint->aspectRatio();
                        })
                        ->resizeCanvas(round($h/$ratio2), $h)
                        ->resize($w, $h);
                    }
                }else{
                    //Lay theo chieu rong
                    if($size_w <= $w) {//neu anh goc nho hon
                        $i->resize($size_w, null, function($constraint) {
                           $constraint->aspectRatio();
                        })
                        ->resizeCanvas($size_w, round($size_w*$ratio2));
                    }else{//Neu anh goc to hon thi
                        $i->resize($w, null, function($constraint) {
                           $constraint->aspectRatio();
                        })
                        ->resizeCanvas($w, round($w*$ratio2))
                        ->resize($w, $h);
                    }
                }
                $i->save(public_path('images/'.$sm));
                return $sm;
            }else{
                return $sm;
            }
        }else{
            $path = 'no-image-available.png';
            return $path;
        }
    }

    static public function get_articles_by_user($user_id, $start, $end)
    {
        $articles = LRedis::ZREVRANGE('articles_by_user:' . $user_id, $start, $end);
        $posts = array();
        foreach ($articles as $value) {
            array_push($posts, json_decode(Article::_buildPost($value)));
        }
        return $posts;
    }


    static public function is_between_to_date($current, $start, $end)
    {
        if($current >= $start && $current <= $end) {
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * @param
     */

    static public function get_strtotime_this_week()
    {
        $result = array();
        $data = self::this_week_range();
        $result['start'] = strtotime($data['start'] . ' 00:00:00');
        $result['end'] = strtotime($data['end'] . ' 23:23:23');
        return $result;
    }

    static public function this_week_range() {
        $datestr = date('Y-m-d');
        date_default_timezone_set (date_default_timezone_get());
        $dt = strtotime ($datestr);
        return array (
            "start" => date ('N', $dt) == 0 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('last sunday', $dt)),
            "end" => date('N', $dt) == 7 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('next saturday', $dt))
        );
    }

    static public function last_week_range() {
        $date = date('Y-m-d');
        $ts = strtotime("$date - 7 days");
        $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
        return array(
              'start' => date('Y-m-d', $start),
              'end' => date('Y-m-d', strtotime('next saturday', $start))
        );
    }

    static public function get_strtotime_last_week()
    {
        $result = array();
        $data = self::last_week_range();
        $result['start'] = strtotime($data['start'] . ' 00:00:00');
        $result['end'] = strtotime($data['end'] . ' 23:23:23');
        return $result;
    }

    /**
     * @param
     */
    static public function get_this_month()
    {
        $data = array();
        $first = date("Y-m-d", strtotime("first day of this month"));
        $last = date("Y-m-d", strtotime("last day of this month"));
        $data['start'] = $first;
        $data['end'] = $last;
        return $data;
    }

    static public function get_strtotime_this_month()
    {
        $data = array();
        $d = self::get_this_month();
        $data['start'] = strtotime($d['start'] . ' 00:00:00');
        $data['end'] = strtotime($d['end'] . ' 23:23:23');
        return $data;
    }

    static public function get_last_month()
    {
        $data = array();
        $first = date("Y-m-d", strtotime("first day of last month"));
        $last = date("Y-m-d", strtotime("last day of last month"));
        $data['start'] = $first;
        $data['end'] = $last;
        return $data;
    }

    static public function get_strtotime_last_month()
    {
        $data = array();
        $d = self::get_last_month();
        $data['start'] = strtotime($d['start'] . ' 00:00:00');
        $data['end'] = strtotime($d['end'] . ' 23:23:23');
        return $data;
    }

    static public function get_next_month()
    {
        $data = array();
        $first = date("Y-m-d", strtotime("first day of next month"));
        $last = date("Y-m-d", strtotime("last day of next month"));
        $data['start'] = $first;
        $data['end'] = $last;
        return $data;
    }

    static public function get_strtotime_next_month()
    {
        $data = array();
        $d = self::get_next_month();
        $data['start'] = strtotime($d['start'] . ' 00:00:00');
        $data['end'] = strtotime($d['end'] . ' 23:23:23');
        return $data;
    }


    /**
     * @param 1 - array user data
     * @param 2 - $is_id = true or false (options)
     * @param 3 - $is_code = true or false (options)
     * @param 4 - $is_email = true or false (options)
     * @param 5 - $is_fb = true or false (options)
     * @param 6 - $is_phone = true or false (options)
     * use: Helper::buildUser($user, $id, $code, $email, $fb, $phone)
     */
    static public function buildUser($data, $is_id=false, $is_code=false, $is_email=false, $is_fb = false, $is_phone = false)
    {
        $id = ($is_id) ? $data->id : null;
        $code = ($is_code) ? $data->code : null;
        $email = ($is_email) ? $data->email : null;
        $fb = $is_fb ? $data->profile->facebook_url : null;
        $phone_number = $is_phone ? $data->profile->phone_number : null;
        $user = array();
        array_push($user, [
            'id' => $id,
            'code' => $code,
            'email' => $email,
            'name' => $data->name,
            'avatar' => $data->avatar,
            'slug' => $data->slug,
            'gender' => $data->gender,
            'profile' => [
                'quote' => $data->profile ? $data->profile->quote : null,
                'facebook_url' => $fb,
                'phone_number' => $phone_number
            ]
        ]);
        return $user;
    }
}
