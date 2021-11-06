<?php

namespace App\Models;
use App\Helpers\Paths;
use Illuminate\Database\Eloquent\Model;

class VideoModel extends Model
{
    protected $table = 'video';


    protected $appends = [
        'video_path',
        'video_gif_path',
        'last_updated',
    ];
}
