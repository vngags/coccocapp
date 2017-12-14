<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attachment;
use Helper;
use Input;
use File;
use Auth;

class AttachmentController extends Controller
{

    public function get_attachments(Request $request)
    {
        $this->validate($request, [
            'attachments' => 'required|array'
        ]);
        $attachs = Attachment::whereIn('id', $request->attachments)->get();
        return $attachs;
    }

    public function get_media()
    {
        $type = Input::get('type');//type=avatar || =photo
        if($type && $type == 'avatar') {
           $media = Attachment::select('url', 'type')
                    ->where('user_id', Auth::user()->id)
                    ->where('type', 'avatar')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(12);
        }else{
           $media = Attachment::select('url', 'type')
                    ->where('user_id', Auth::user()->id)
                    ->where('type', 'photo')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(12);
        }
         return $media;
    }

    public function upload(Request $request)
    {
        $type = $request->type;
        $directory = Auth::user()->id . Auth::user()->code;
        switch ($type) {
           case 'avatar':
                $this->validate($request, [
                    'image' => ['required', 'regex:/^data:image\/[^;]+;base64,/i']
                ]);
                $filename = $directory . '_avatar_' . mt_rand(1111, 9999) . '_' . date('dmy_his');
                $image = Helper::save_attachment($request->image, 'avatar', $directory, $filename, str_slug(Auth::user()->name));
                if($image) {
                    $attachment = new Attachment();
                    $attachment->url = $image;
                    $attachment->type = 'avatar';
                    $request->user()->attachments()->save($attachment);
                    return response()->json([
                      'status' => 'success',
                      'image' => $image
                    ]);
               }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Có lỗi trong quá trình tải ảnh lên!'
                    ]);
               }
                break;
           default:
                $this->validate($request, [
                  'image' => 'required|image|max:4000'
                ]);
                $filename = $directory . '_' . mt_rand(1111, 9999) . '_' . date('dmy_his');
                $image = Helper::save_attachment(Input::file('image'), 'file', $directory, $filename, str_slug(Auth::user()->name));
                if($image) {
                    //save also to File table
                    $attachment = new Attachment();
                    $attachment->url = $image;
                    $attachment->type = 'photo';
                    $request->user()->attachments()->save($attachment);

                    $files = array();
                    $files[] = array('url' => url('image/full/full/'.$image));
                    $files[] = array('short' => $image);
                    $files[] = array('attachment_id' => $attachment->id);
                    //$files[] = array('image' => $image);
                    $response = array('files' => $files);
                    return  json_encode($response);
               }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Có lỗi trong quá trình tải ảnh lên!'
                    ]);
               }
               break;
           }//end switch
    }

    public function delete(Request $request)
    {
        if($request->has('image')) {
               $this->validate($request, [
                 'image' => 'required'
               ]);
               $image = Attachment::where('url', $request->image)->first();
               if($image) {
                     if(File::exists('images/'.$request->image)) {
                         File::delete('images/'.$request->image);
                     }

                     $blog_attachment = \App\BlogAttachment::where('attachment_id', $image->id)->first();
                     if($blog_attachment) {
                         $blog_attachment->delete();
                     }
                     $image->delete();
                     return response()->json([
                       'status' => 'deleted'
                     ]);
               }
       }elseif($request->has('file')) {
                $this->validate($request, [
                    'file' => 'required|max:100'
                ]);

                $name = basename($request->file); // return name and ext
                //Get member directory Eg: 1
                $dirname =pathinfo($request->file, PATHINFO_DIRNAME); //return http://coccoc.me/images/1
                $parts = explode('/',$dirname);//create array
                $directory = array_pop($parts); //return Eg: 1
                $filename = $directory . '/' . $name;

                $image = Attachment::where('user_id', Auth::user()->id)
                        ->where('url', $filename)
                        ->first();

                //get sm url
                $sm_name = explode('.',$name);//return filename,jpg
                $sm = $directory . '/' . $sm_name[0] . '_w300.' . $sm_name[1];
                if($image) {
                    if(File::exists('images/'.$filename)) {
                        File::delete('images/'.$filename);
                    }
                    if(File::exists('images/'.$sm)) {
                        File::delete('images/'.$sm);
                    }
                    $image->delete();
                    return response()->json([
                     'status' => 'deleted',
                     'attachment_id' => $image->id
                    ]);
                }
                return response()->json([
                  'status' => 'error'
                ]);
        }

       return response()->json([
          'status' => 'error'
       ]);
    }

    public function check_used_image(Request $request)
    {
        $this->validate($request, [
          'image' => 'required'
        ]);
        $image = Attachment::where('url', $request->image)->first();
        if($image) {
            $blog_attachment = \App\BlogAttachment::where('attachment_id', $image->id)->first();
            if($blog_attachment) {
                $post = Blog::find($blog_attachment->blog_id);
                return response()->json([
                    'status' => 'used',
                    'title' => $post->title,
                    'url' => route('blog.show', ['slug' => $post->slug . '-' . $post->id])
                ]);
            }
            return response()->json([
                 'status' => 'avalible'
            ]);
        }
        return response()->json([
             'status' => 'avalible'
        ]);
    }

}
