<?php

namespace App\Http\Controllers\MediaIntegration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Paths;
use Exception;
use App\Models\VideoModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use File;
use Str;

class VideoController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }




public function videos(){
    try {
        $videos = VideoModel::all();
        $countVideo = count($videos);
        $data = [
            "page" => "videos",
            "videos" => $videos, 
            "countVideo" => $countVideo
        ];
        return view('Media.video', $data);
        
    } catch (Exception $error) {
       Log::info('MediaIntegration\VideoController@videos error message: ' . $error->getMessage());
       $message = "Unable to get Resource";
       return $message;
    }
}



public function videoUploadHandler(Request $request){
    try {
        if($request->hasFile('video')){
            $name = request()->file('video')->getClientOriginalName();
            if($name == "blob"){
                $name = 'video_rec'.time().'.webm';
            }

            $video = new VideoModel();
            $video->user_id = Auth::id();
            $video->uuid = \Str::uuid();
            $video->file = $this->storeVideo('video');
            $video->name = $name;
            $video->save();
            $message = "Upload Successful!";
            return response([
                "message" =>$message,
                "url" => route('user.video.edit', $video->uuid)
            ], 200);

        }
    } catch (Exception $error) {
        Log::info('MediaIntegration\VideoController@videoUploadHandler error message: ' . $error->getMessage());
        $message = "Unable to Upload video";
        return $message;
    }

}



public function updateVideoDetails(Request $request){
    try {
        $video = VideoModel::where('user_id', Auth::id())->where('id', $request->video_id);
        if(!$video){
            $message = "Video not found!";
            return response()->json(["message" => $message], 404);
        }
        $video->title =  $request->title;
        $video->category = $request->category ? $request->category : $video->category;
        $video->contributor = $request->contributor ? $request->contributor : $video->contributor;
        $video->save();
        $message = "Update Successful!";
        return response()->json(["message" => $message], 200);

       
    } catch (Exception $error) {
        Log::info("MediaIntegration\VideoController@updateVideoDetails error message:" . $error->getMessage());
        $message = 'Unable to get Resource. Encountered an error.';
        return $error;

    }
}

public function editVideo($videouuid){
    try {

        $video = VideoModel::where('uuid', $videouuid)->first();
        if (!$video) {
            $message = 'Video not found!';
            return response()->json(["message" => $message], 404);
        }
        $data = [
            "page" =>  "videos", 
            "sub" =>  "videos",
            "video" => $video
        ];
        return view('App.Video.edit', $data);

    } catch (Exception $error) {
        Log::info('VideoController@editVideo error message: ' . $error->getMessage());
        $message = 'Unable to get Resource. Encountered an error.';
        return $message;
    }
}


public function downloadVideo($file){

    $file_path = storage_path('app/' . Paths::VIDEO_PATH . $file);
    $header = ['Content-Type' => 'application/mp4, application/webm'];

    return response()->download($file_path, $file, $header);
}

private function storeVideo($key, $video = null)
{
    $filename = '';

    if (request()->hasFile($key)) {
        $name = request()->file($key)->getClientOriginalName();
        if($name == "blob"){
            $name = 'video_rec'.time().'.webm';    
        }
        $subject = $name;
        $secondName = "upload-".time().".mp4";
        $filename = str_replace(' ', '_',  $subject);
        $path = Paths::VIDEO_PATH;
        $imagePath = "{$path}{$filename}";
        $secondPath = "{$path}{$secondName}";

        if($video){
            $this->deleteVideo($video->file);
        }
        $getPath = storage_path()."/app/$imagePath";
        $getSecPath = storage_path()."/app/$secondPath";
        Storage::put($imagePath, File::get(request()->file($key)));

      Storage::put($getSecPath, File::get(request()->file($key)));
      exec("ffmpeg -i $getPath  $getSecPath");

    }
    return $secondName;
}


}
