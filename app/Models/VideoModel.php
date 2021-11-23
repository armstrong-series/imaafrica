<?php

namespace App\Models;

use App\Helpers\Paths;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class VideoModel extends Model
{
    protected $table = 'video';

    protected $appends = [
        'video_path',
        
    ];


    public function getVideoPathAttribute(){
        $video = Paths::VIDEO_PATH .$this->file;
        if(Storage::has($video)){
            $path = Request::root().Storage::disk('local')->url($video);
            return $path;

        }
    }


}
