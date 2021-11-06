<?php

namespace App\Models;
use App\Helpers\Paths;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class AudioModel extends Model
{
    protected $table = 'audio';



    protected $appends = ['media_path'];

    public function getMediaPathAttribute(){
         $audioPath = Paths::PLAYLIST_PATH .$this->file;
        //  return $audioPath;
         if(Storage::has($audioPath)){
             return  Storage::url($audioPath);
 
         }else{
             return '';
         }
    }
}
