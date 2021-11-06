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
use FFMpeg;


class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }




public function videos(){
    try {
        $videos = VideoModel::all();
        $countVideo = count($videos);
        $data = [
            "page" => "videos",
            "videos" => $videos, 
            
        ];
        return view('Media.video.index', $data);
        
    } catch (Exception $error) {
       Log::info('MediaIntegration\VideoController@videos error message: ' . $error->getMessage());
       $message = "Unable to get Resource";
       return $message;
    }
}



public function videoUploadHandler(Request $request){
    try {

        dd($request->all());
        if($request->hasFile('video')){
            $name = request()->file('video')->getClientOriginalName();
            // if($name == "blob"){
            //     $name = 'video_rec'.time().'.webm';
            // }
            $video = new VideoModel();
            $video->user_id = Auth::id();
            $video->uuid = \Str::uuid();
            $video->file = $this->storeVideo('video');
            $video->name = $name;
            $video->save();
            $createGif = $this->generateVideoGif($video);
            if($createGif['status'] == true){
                $video->video_gif = $createGif['gif'];
                $video->save();
            }
           
            return response([
                "status" => "success",
                "message" => "Upload Successful!",
                "url" => route('user.video.edit', $video->uuid)
            ], 200);

        }
    } catch (Exception $error) {
        Log::info('MediaIntegration\VideoController@videoUploadHandler error message: ' . $error->getMessage());
        $message = "Unable to Upload video";
        return $message;
    }

}



private function generateWatermark($videoSource, $extension, $watermark = "")
{
    $ffmpeg = FFMpeg\FFMpeg::create();

    $video = $ffmpeg->open($videoSource);
    $format = new FFMpeg\Format\Video\X264('libmp3lame', 'libx264');

    if (!empty($watermark)){
        $video->filters()->watermark($watermark, array(
                    'position' => 'relative',
                    'top' => 25,
                    'right' => 50,
                ));
    }

    $format-> setKiloBitrate(1000)-> setAudioChannels(2)
    -> setAudioKiloBitrate(256);

    $randomFileName = rand().".$extension";
    $saveLocation = getcwd(). '/video/'.$randomFileName;
    $image = Storage::path(Paths::VIDEO_WATERMARK_PATH  . $randomFileName);
    $video->save($format, $saveLocation);

    if (file_exists($saveLocation))
        return "http://localhost/test/video/$randomFileName";
    else
        return "http://localhost/test/thumb/404.png";

}

// echo $videoLocation =  processVideo("sample.mp4","mp4","favicon.png");

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


public function uploadPage(){
    // $data = [
    //     "page" =>  "videos", 
    //     "sub" =>  "videos",
        
    // ];


    return view('Media.video.upload');
}

private function generateVideoGif($storedVideo){
    if (config('app.dev_os') != 'linux') {
        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/local/bin/ffprobe',
        ]);
    } else {
        $ffmpeg = \FFMpeg\FFMpeg::create();
    }


    $storedVideoPath = Storage::path(Paths::VIDEO_PATH . $storedVideo->file);
    $gifName = str_replace(' ', '_', $storedVideo->name) . '.gif';
    $gifPath = Storage::path(Paths::VIDEO_GIF_PATH . $gifName);
    $savePath = public_path() . '/' . $gifName;
    $video = $ffmpeg->open($storedVideoPath);
    $file = $video->gif(\FFMpeg\Coordinate\TimeCode::fromSeconds(1),
     new \FFMpeg\Coordinate\Dimension(640, 480), 5)->save($gifName);
     rename($savePath, $gifPath);
     return [
         'status' => true,
         'gif' => $gifName,
     ];

}

private function storeVideo($key, $video = null)
{
    $filename = '';
    
   if(!request()->hasFile($key)){

        $message = "Please add a file!";
        return response()->json(['message' => $message], 400);
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
            $getPath = storage_path()."/app/$imagePath";
            $getSecPath = storage_path()."/app/$secondPath";
            Storage::put($imagePath, File::get(request()->file($key)));
            exec("ffmpeg -i $getPath  $getSecPath");

        }
        return $secondName; 
   }
}


}
