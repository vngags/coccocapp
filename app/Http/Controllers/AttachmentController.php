<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use File;
use Image;
use Response;

class AttachmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('showImage');
    }

    public function showImage($w, $h, $src)
    {
        //using Intervention Image Cache in https://github.com/Intervention/imagecache and add use Response;
        //using: http://coccoc.me/image/{width}/{height}/{src} => full/full: full size, size/auto: width = size, height = ratio
        $img_path = public_path('images/'.$src);
        if(!File::exists($img_path)) {
            $img_path = public_path('images/no-image.jpg');
        }
        // CHECK TYPE FILE
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $img_path);
        finfo_close($finfo);
        // END CHECK TYPE FILE
        if($mime === 'image/gif') {
            // REDIRECT IF FILE GIF
            return redirect('images/' . $src);
        }

        $img =  Image::cache(function($image)use($w,$h,$img_path) {
            if($w == 'full' && $h == 'full') {
                return $image->make($img_path);
            }
            if($h == 'auto') {
                return $image->make($img_path)->resize($w, null, function($constraint) {
                   $constraint->aspectRatio();
                });
            }
            //resize full ratio
            $i = Image::make($img_path);
            $size_w = $i->width();
            $size_h = $i->height();
            $ratio1 = $size_h/$size_w;
            //Dau tien tinh xem hinh can cat la doc hay ngang
            $ratio2 = $h/$w;
            if($ratio1 <= $ratio2) {//Truong hop hinh goc rong chieu ngang hon hinh muon cat
                // //ta lay theo chieu dai
                if($size_h <= $h) {
                    return $image->make($img_path)
                    ->resize(null, $size_h, function($constraint) {
                       $constraint->aspectRatio();
                    })
                    ->resizeCanvas(round($size_h/$ratio2), $size_h);
                }else{
                    return $image->make($img_path)
                    ->resize(null, $h, function($constraint) {
                       $constraint->aspectRatio();
                    })
                    ->resizeCanvas(round($h/$ratio2), $h)
                    ->resize($w, $h);
                }
            }else{
                //Lay theo chieu rong
                if($size_w <= $w) {//neu anh goc nho hon
                    return $image->make($img_path)
                    ->resize($size_w, null, function($constraint) {
                       $constraint->aspectRatio();
                    })
                    ->resizeCanvas($size_w, round($size_w*$ratio2));
                }else{//Neu anh goc to hon thi
                    return $image->make($img_path)
                    ->resize($w, null, function($constraint) {
                       $constraint->aspectRatio();
                    })
                    ->resizeCanvas($w, round($w*$ratio2))
                    ->resize($w, $h);
                }
            }
        });
        return Response::make($img, 200, ['Content-Type' => $mime]);
    }

}
