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
   
        $this->middleware('auth')->except("videos");
    }


public function videos(){
    try {
       
        $video = VideoModel::all();
       
        $data = [
            "page" => "videos",
            "video" => $video, 
            
        ];
        return view('Media.video.index', $data);
        
    } catch (Exception $error) {
       Log::info('MediaIntegration\VideoController@videos error message: ' . $error->getMessage());
       $message = "Unable to get Resource";
       return $message;
    }
}

public function getVideo(Request $request){
    try {
       
        $video = VideoModel::where('uuid', $request->uuid)->first();
            if(!$video){
                $message = "Video not found!";
                   return response()->json(["message" => $message], 404);
            }

        return response()->json(["video" => $video,"status" => "success"], 200);
        
        
    } catch (Exception $error) {
       Log::info('MediaIntegration\VideoController@getVideo error message: ' . $error->getMessage());
       $message = "Unable to get Resource";
       return $message;
    }
}



public function videoUploadHandler(Request $request){
    try {
        if (!$request->file('video')) {
            $message = "Add a Video !";
            return response()->json(['message' => $message], 400);
        }

        if($request->hasFile("video")){
            $video_path = storage_path('app/' . Paths::VIDEO_PATH);
            $extension = $request->file('video')->getClientOriginalExtension();

            if (in_array(strtolower($extension), ["mp4", "mov", "webm"])) {
                $fileName = (string) "IMAAFRICA--" . time() . '.' . $extension;
                $request->file('video')->move($video_path, $fileName);
                $video = new VideoModel();
                $video->user_id = Auth::id();
                $video->file =  $fileName;
                $video->uuid = \Str::uuid();
                $video->save();

                // $createGif = $this->generateVideoGif($video);

                // if($createGif['status'] == true){
                //     $video->video_gif = $createGif['gif'];
                //     $video->save();
                // }

                $watermark = asset("img/imaafica.png");
                $videoWatermark = $this->generateVideoWatermark($request->file('video'), $extension, $watermark);
                if($videoWatermark['status'] == true){
                    $video->video_watermark =  $videoWatermark['watermark'];
                    $video->save();
                }
               
                $message = "Upload Successful!";
                
                return response()->json([
                    "message" => $message,
                    "video" => $video,
                     "url" => route('user.video.edit', $video->uuid)
                    ], 200);

            }
            
            else {
                $message = "Invalid file format!";
                return response()->json(['message' => $message], 400);
             }
             
            }
       
    } catch (Exception $error) {
        Log::info('MediaIntegration\VideoController@videoUploadHandler error message: ' . $error->getMessage());
        $message = "Unable to Upload video";
            return response()->json([
            "message" => $message,
            "error" => $error,
            ],500);
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
        return view('Media.video.edit', $data);

    } catch (Exception $error) {
        Log::info('MediaIntegration\VideoController@editVideo error message: ' . $error->getMessage());
        $message = 'Unable to get Resource. Encountered an error.';
        return $message;
    }
}




private function generateVideoWatermark($videoSource, $extension, $watermarkPath = "")
{

    if (config('app.dev_os') != 'linux') {
        $ffmpeg = \FFMpeg\FFMpeg::create([
            // 'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
            // 'ffprobe.binaries' => '/usr/local/bin/ffprobe',
            'ffmpeg_binaries' => '/usr/bin/ffmpeg', 
            'ffprobe_binaries' => '/usr/bin/ffprobe', 
        ]);
    } else {
        $ffmpeg = \FFMpeg\FFMpeg::create();
    }
    // $ffmpeg = FFMpeg\FFMpeg::create();
    $video = $ffmpeg->open($videoSource);
    $format = new FFMpeg\Format\Video\X264('libmp3lame', 'libx264');

    if (!empty($watermark)){
        $video->filters()->watermark($watermarkPath, array(
            'position' => 'relative',
            'top' => 50,
            'right' => 50,
        ));
    }

    $format-> setKiloBitrate(1000)
    -> setAudioChannels(2)
    -> setAudioKiloBitrate(256);

    $randomFileName = "Imaafrica-watermark".rand().".$extension";
    $image_path = Storage::path(Paths::VIDEO_WATERMARK_PATH  . $randomFileName);
    $video->save($format, $image_path);
   
    // if (file_exists($image_path))
    //     return "http://localhost/test/video/$randomFileName";
    // else
    //     return "http://localhost/test/thumb/404.png";

}

// echo $videoLocation =  processVideo("sample.mp4","mp4","favicon.png");

public function updateVideoDetails(Request $request){
    try {

        // dd($request->all());
        $video = VideoModel::where('uuid', $request->uuid)->first();
        if(!$video){
            $message = "Video not found!";
            return response()->json(["message" => $message], 404);
        }
        $video->title =  $request->title ? $request->title : $video->title;
        $video->category = $request->category ? $request->category : $video->category;
        $video->contributor = $request->contributor ? $request->contributor : $video->contributor;
        $video->save();
        $message = "Video Update Successful!";
        return response()->json(["message" => $message, "video" => $video], 200);

       
    } catch (Exception $error) {
        Log::info("MediaIntegration\VideoController@updateVideoDetails error message:" . $error->getMessage());
        $message = 'Unable to get Resource. Encountered an error.';
        return $error;

    }
}




public function deleteVideo(Request $request)
{
    try {

        $video = VideoModel::where('id', $request->video_id)->first();
        if (!$video) {
            $message = "Unknown Video!";
            return response()->json(['message' => $message], 404);
        }
        $video_path = Paths::VIDEO_PATH . $video->file;
        if (Storage::has($video_path)) {
            Storage::delete($video_path);
        }

        $video->delete();
        $message = "Delete Completed!";
        return response()->json(["message" => $message], 200);

    } catch (Exception $error) {
        Log::info('MediaIntegration\VideoController@deleteVideo error message: ' . $error->getMessage());
        $message = 'Sorry, unable to create template. Please try again';
        return response()->json([
            'error' => true,
            "message" => $message,
        ], 500);
    }
}



public function downloadVideo($file){

    $file_path = storage_path('app/' . Paths::VIDEO_PATH . $file);
    $header = ['Content-Type' => 'video/*'];

    return response()->download($file_path, $file, $header);
}


public function uploadPage(){
   
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
