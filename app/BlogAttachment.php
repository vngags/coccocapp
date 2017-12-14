<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogAttachment extends Model
{
    protected $fillable = ['blog_id', 'attachment_id'];

    public $timestamps = false;

    static public function destroy($id)
    {
        try {
            $ids = explode(",", $id);
            self::find($ids)->each(function($item, $key) {
                $item->delete();
            });
        } catch (Exception $e) {
            dd($e);
        }

    }

}
